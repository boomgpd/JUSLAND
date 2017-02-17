<?php 
namespace Home\Controller;
use Think\Controller;
use Home\Model\MemberModel;
use Home\Model\PictureModel;
use Home\Model\BoardListModel;
use Home\Model\BoardModel;
use Home\Model\DiscussModel;
use Common\Model\BespokeModel;
use Common\Model\AreaModel;

/**
 * 创建瀑布流控制器
 */
class WaterfallController extends CommonController {
	protected $member;
	protected $picture;
	protected $board_list;
	protected $board;
	protected $discuss;
	protected $bespoke;
	protected $area;
	
	
	public function __construct (){
		parent::__construct();
		$this->member= new MemberModel;
		$this->picture= new PictureModel;
		$this->board_list = new BoardListModel;
		$this->board = new BoardModel;
		$this->area = new AreaModel;
		$this->bespoke = new BespokeModel;
	}
	
	/**
	 *创建首页控制器
	 */
    public function index(){
		/**
		 * 1.获取用户信息
		 * 2.获取用户转载瀑布流信息
		 * 3.获取所有后台上传瀑布流消息
		 */
//		 $this->picture->up_show_num();die;
		 $data['member'] = $this->member->getData();
		 $data['board'] = $this->board->getData();
		 $data['area'] = $this->area->getData();
		 $arr = I('get.');
		 unset($arr['web_type']);
		 unset($arr['m']);
		 $this->assign('session',$_SESSION);
		 $this->assign('data',$data);
		 $this->assign('q',I('get.q'));
		 if(IS_AJAX){
			 $area_id = I('post.area_id');
			 $re = $this->area->getData($area_id);
			 $this->ajaxReturn($re);exit;
		 }
		 
		 $type = M('type')->where(array('is_del'=>0))->order('sort asc')->select();
		 $this->assign('type',$type);
    	 $this->display();
    }
	/*
	 * 1创建填加画板的方法；
	 * 2需要去到模块里验证；
	 **/
	public function addboard(){
		if(!IS_AJAX) return FALSE;
		$data = I('post.board_name');
		$arr  = array(
		'board_name'=>$data,
		'member_member_id'=>$_SESSION['member_id']
		);
		$re = $this->board->addData($arr);
		if($re){
//			$data = $this->board->getData();
			$this->ajaxReturn($re);exit;
		}else{
			$this->ajaxReturn($error);exit;
		}
	}
	
	/**
	 * 创建采集方法
	 */
	public function collect(){
		$data = I('post.');
		$arr= array(
			'collect_time'=>time(),
			'title'=>$data['title'],
			'picture_p_id'=>$data['picture_p_id'],
			'board_board_id'=>$data['board_id']
			/*'board_list_id'=>$data['board_list_id']*/
		);
		$re = $this->board_list->add_collect($arr);
		$this->ajaxReturn($re);exit;
	}
	
	/**
	 * 创建详情页面
	 * 思路 
	 * 1.获取详情页的信息
	 * 2.显示详情页相关信息
	 * details(详情页的意思)
	 */
	 public function details(){
		$data['member'] = $this->member->getData();
		$data['get']=$_GET['id'];
		$data['picture'] = $this->picture->getxs($data['get']);
		$data['discuss'] = $this->discuss->dis_sect($data['get']);
	 	$this->assign('data',$data);
	 	$this->display();
	 }
	 
	 
	public function discuss(){
		if(!IS_AJAX) return FALSE;
		$data = I('post.');
		$arr= array(
			'discuss_time'=>time(),
			'discuss_content'=>$data['data'],
			'board_list_board_id'=>$data['tid'],
			'member_member_id'=>$_SESSION['member_id']
		);
		$re = $this->discuss->dis_mo($arr);
		$this->ajaxReturn($re);exit;
	}
	
	/**
	 * 完成采集画面信息的方法
	 */
	 public function perfect(){
		if(!IS_AJAX) return FALSE;
		if(!$_SESSION['member_id']){
			$arr = array('name'=>'remind','content'=>'您还没有登录，请您登录后转载！');
			$this->ajaxReturn($arr);exit;
		}
	 	$data = I('post.');
		if($data['p_id']){
			$re = $this->picture->pic($data['p_id']);
			$re['board_list_id'] = $data['board_list_id'];
			$this->ajaxReturn($re);exit;
		}else{
			$re = $this->board_list->board_list_($data['board_list_id']);
			$re['board_list_id'] = $data['board_list_id'];
			$this->ajaxReturn($re);exit;
		}
	 }
	 
//	 灵感库详情展示页面
	 public function qing($p_id){
	 	$data['member'] = $this->member->getData();
		$data['picture'] = $this->picture->getDatas($p_id);
		$this->assign('data',$data);
		if($_GET['p_id']){
			$re=$this->picture->photo($_GET['p_id']);
		}else{
			$re=$this->board_list->photo($_GET['board_list_id']);
		}
		$zan = M('water_click')->where(array('member_id'=>$_SESSION['member_id']))->getField('picture_id',true);
		$tui = implode(',',$zan);
		$this->assign('tui',$tui);
		$this->assign('re',$re);
	 	$this->display();
	 }
	 
	 
	 /**
	  * 创建灵感的预约的方法
	  */
	  public function insp_make(){
	  	$data           = I('post.');
		$data['b_name'] = '灵感库';
		$data['url1']   = $this->upload();
		$re = $this->bespoke->store_mo($data);
		if(!$re){
			echo '<script type="text/javascript">alert("预约失败");location.href = "'.U('home/waterfall/index').'"</script>';
		}else{
			echo '<script type="text/javascript">alert("预约成功，工作人员会在12小时内与您取得联系，请保持手机畅通；");location.href = "'.U('home/waterfall/index').'"</script>';
		}
	  }
	  
	/**
	 * 创建上传类
	 */
	public function upload(){
		    $upload = new \Think\Upload();// 实例化上传类    
		    $upload->maxSize   =     3145728 ;// 设置附件上传大小    
		    $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型    
		    $upload->savePath  =      './Public/Uploads/bespoke'; // 设置附件上传目录    
		    // 上传单个文件    
		     $info   =   $upload->uploadOne($_FILES['photo1']);    
		     if(!$info) {
		     	// 上传错误提示错误信息       
		     	 $this->error($upload->getError());    
			 }else{
			 	// 上传成功 获取上传文件信息         
			 	return $info['savepath'].$info['savename'];    
			 }
	}
	
	/**
	 * 创建获取分页数据方法
	 */
	 public function loading(){
	 	/**
		 * 1.获取页码；
		 * 2.获取对应数据；
		 */
	 	$arr = I('post.');
	 	if($arr['member_id']){
	 		$data = false;
	 	}else{
			$data = $this->picture->getData($arr);
	 	}
		if(!$data){
			$this->ajaxReturn(0);exit;
		}else{
			$this->assign('data',$data);
			$content = $this->fetch();
			$data = explode('chaifen',$content);
			$data = array_filter($data);
			$this->ajaxReturn($data);exit;
		}
		
	 }
	 
	
}