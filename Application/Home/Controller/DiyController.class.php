<?php
namespace Home\Controller;
use Common\Model\Diy_marrierModel;
/**
 * geyu
 * 8.11
 * 创建画板具体页
 */
class DiyController extends CommonController {

	protected $marrier;

	public function __construct() {
		parent::__construct();
		$this->marrier = new Diy_marrierModel;
	}

	//首页
	public function index(){
		//获得站点配置
		$data = M('Site_config')->join('site_config_type ON site_config.site_config_type_id = site_config_type.site_config_type_id') ->where(array('site_config_type.type_name'=>'婚礼人首页'))->select();
		// $data = array_reverse($data);
		$this->assign('data',$data);
		// dump($data);die;
		$this->display();
	}

	//列表页
	public function lists($type_id=null){
		//获得列表页数据
		$city_id = $_SESSION['location_city_id'];
		$data = $this->marrier->lists_data($type_id,$city_id);
		$this->assign('data',$data['data']);
		$this->assign('show',$data['show']);
		$this->assign('page_type',$data['type']);
		//获得当前用户收藏的数据
		$member_id = $_SESSION['member_id'];
		$collect = M('Diy_marrier_collect')->where(array('user_id'=>$member_id))->getField('diy_marrier_id',true);
		$collect = implode(',',$collect);
		$this->assign('collect',$collect);
		//获得当前用户点赞的数据
		$love = M('Diy_marrier_click')->where(array('user_id'=>$member_id))->getField('diy_marrier_id',true);
		$love = implode(',',$love);
		$this->assign('love',$love);
		$this->display();
	}

	//详情信息页
	public function info($id){
		$data = $this->marrier->where(array('diy_marrier_id'=>$id))->find();
		$msgData = M('Diy_marrier_message')->where(array('diy_marrier_id'=>$id))->find();
		$data = array_merge($msgData,$data);
		//没上线或被删除
		if(!$data['status'] || $data['is_del'] || !$data['is_apply']){
			echo '<script type="text/javascript">alert("婚礼人不存在或已下线");location.href = "'.U('Home/Diy/lists').'";</script>';die;
		};
		$data['showed'] = explode(',',$data['showed']);
		$data['remark'] = explode('|',$data['remark']);	
		$this->assign('data',$data);
		//获得当前用户收藏的数据
		$member_id = $_SESSION['member_id'];
		$collect = M('Diy_marrier_collect')->where(array('user_id'=>$member_id))->getField('diy_marrier_id',true);
		$collect = implode(',',$collect);
		$this->assign('collect',$collect);
		//获得推荐栏数据
		$sql = array(
			'diy_marrier.is_apply'=>1,
			'diy_marrier.is_del'=>0,
			'diy_marrier_message.status'=>1,
			'diy_marrier.diy_marrier_id'=>array('NEQ',$id),
		);
		$chooseData = M('Diy_marrier')->join('LEFT JOIN diy_marrier_message ON diy_marrier.diy_marrier_id = diy_marrier_message.diy_marrier_id')->where($sql)->select();
		$chooseData = array_slice($chooseData,0,4);
		$this->assign('chooseData',$chooseData);
		$this->display();
	}

	//处理收藏和喜欢方法
	public function deal($id,$action,$table){
		if(!IS_AJAX || !isset($id) || !isset($action) || !isset($table)) return FALSE;
		$member_id = $_SESSION['member_id'];
		if(!isset($member_id)){
			echo 'no_login';die;
		};
		//判断操作的是哪个表
		if($table == 'collect'){
			$model = M('Diy_marrier_collect');
		}else{
			$model = M('Diy_marrier_click');
		};

		//执行不一样的操作
		if($action == 'add'){
			$re = $model->where(array('diy_marrier_id'=>$id,'user_id'=>$member_id))->find();
			if(isset($re)) return FLASE;
			echo $model->add(array('diy_marrier_id'=>$id,'time'=>time(),'user_id'=>$member_id));die;
		}else{
		 	echo  $model->where(array('diy_marrier_id'=>$id,'user_id'=>$member_id))->delete();die;
		};
	}

}
