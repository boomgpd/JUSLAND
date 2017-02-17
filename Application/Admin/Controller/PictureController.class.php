<?php namespace Admin\Controller;
header('Content-Type: text/html; charset=utf-8');
use Think\Controller;
use Admin\Model\PictureModel;
/**
 * 创建后台首页控制器
 * 16/08/03
 * boom
 */
class PictureController extends CommonController {
	protected $picture;
	
	public function __construct(){
		parent::__construct();
		$this->picture = new PictureModel;
	}
	
	/**
	 * 创建瀑布流显示方法
	 */
    public function index(){
    	$data = $this->picture->getData();
//		dump($data);die;
		$this->assign('data',$data);
		$this->display();
    }
	
	/**
	 * 创建添加瀑布流方法
	 */
	public function add(){
		if(IS_POST){
			$data = I("post.");
			$re = $this->picture->store($data);
//			echo 1;die;
			if(!$re){
				echo '<script type="text/javascript">alert("添加失败");window.history.back(-1);</script>';
			}else{
				echo '<script type="text/javascript">alert("添加成功");location.href = "'.U('admin/picture/index').'"</script>';
			}
		}else{
			/**
			 * 判定有没有商家主键参数
			 * 存在的时候查找到对应商家信息
			 * 不存在的时候获取所有商家信息
			 */
			 $merchant_id = I('get.merchant_merchant_id');
			 if($merchant_id){
			 	$merchant_data = M('merchant')->field('m_name,merchant_id')->where(array('merchant_id'))->find();
				$this->assign('merchant_data',$merchant_data);
			 }else{
			 	$sale_data = M('merchant')->field('m_name,merchant_id')->select();
				$this->assign('sale_data',$sale_data);
			 }
			
			/**
			 * 获取所有分类
			 */
			$type = M('type')->select();
			$this->assign('type',$type);
			$add_type = I('get.type');
			if(!$add_type){
				$logo = C('__MERCHANT_LOGO__');
				$this->assign('logo',$logo);
				$this->display('false_add');
			}else{
				$this->display('add');
			}

		}
    }
	
	/**
	 * 创建获取对应标签的方法
	 */
	 public function getLables(){
	 	if(!IS_AIAX) return FALSE;
		$type_tid = I('post.type_tid');
		if(!$type_tid) return FALSE;  //判定如果没有值，返回错误
		$data = M('lable')->where(array('type_tid'=>$type_tid))->select();
		$this->ajaxReturn($data);exit;
	 }
	 
	/**
	 * 构建php的上传方法
	 */
//	public function upload(){
//	    $upload = new \Think\Upload();// 实例化上传类
//	    $upload->maxSize   =     3145728 ;// 设置附件上传大小
//	    $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
//	    $upload->rootPath  =     __ROOT__; // 设置附件上传根目录
//	    $upload->savePath  =     'Picture/'; // 设置附件上传（子）目录
//	    // 上传文件 
//	    $info   =   $upload->upload();
//	    if (empty($info)) {
//	        echo json_encode($this->error());exit;
//	    } else {
//	        //上传成功，把上传好的信息返给js
//	        $data = $info['Filedata']['savepath'].$info['Filedata']['savename'];
//	        echo json_encode($data);exit;
//	    }
//	}
	
	/**
	 * 创建删除方法
	 */
	 public function del(){
	 	if(!IS_AJAX) return FALSE;
		$p_id = I('post.p_id');
		$re = M('picture')->where(array('p_id'=>$p_id))->save(array('is_del'=>1));
		if(!$re){
			$this->ajaxReturn('删除失败');
		}else{
			$this->ajaxReturn('删除成功');
		}
		
	 }
	 
	 /**
	  * 创建编辑方法
	  */
	  public function edit(){
	  	/**
		 * 传输方式为post时进行编辑
		 */
		if(IS_POST){
			$data = I('post.');
			if(!$data['merchant_name'] || !$data['merchant_logo']){
				alert('商家名称和logo必须存在');die;
			};
			if(count($data['url']) != count($data['thumb'])){
				$this->error = '数据异常';
				return FALSE;
			};
			$data['url'] = implode("|", $data['url']);
			$data['thumb'] = implode("|", $data['thumb']);
			$data['lables'] = implode("|", $data['lables']);
			if(!$this->picture->create($data)){
				alert($this->picture->getError());die;
			}
			
			$re = M("picture")->where(array('p_id'=>$data['p_id']))->save($data);
//			dump($data);die;
			if($re || $re === 0){
				alert('编辑成功',U('index'));
			}else{
				alert('编辑失败，请重新编辑');
			}
		}
		
		
		$logo = C('__MERCHANT_LOGO__');
		$this->assign('logo',$logo);
		
	  	$p_id = I('get.p_id');
		/**
		 * 1.获取编辑的瀑布流的信息
		 * 2.获取所有分类
		 * 3.获取该分类所对应的标签
		 */
		$data = M('picture')->where(array('p_id'=>$p_id))->find();
		$data['lables'] = explode("|", $data['lables']);
		$data['type_tid'] = M('lable')->where(array('lid'=>$data['lables'][0]))->getField('type_tid');
		$data['url'] = explode("|", $data['url']);
		$data['thumb'] = explode("|", $data['thumb']);
//		dump($data);die;
		$this->assign('data',$data); 
		$type = M('type')->select();
		$this->assign('type',$type);
		$lables = M('lable')->where(array('type_tid'=>$data['type_tid']))->select();
		$this->assign('lables',$lables);
		$this->display();
	  }
	/**
	 * 创建首页瀑布流推荐的方法
	 */
	public function index_list(){
		$type = M('type')->where(array('is_del'=>0))->limit(4)->select();
		$temp = array();
		foreach($type as $k=>&$v){
			if($k == 0){
				$picture = M('picture_list')->where(array('type_id'=>$v['tid']))->limit(9)->select();
				$adver   = M('adver')->alias('a')->join('__ADVER_TYPE__ b ON a.adver_type_id = b.adver_type_id AND b.adver_type_name = "首页瀑布流广告"')->limit(0,2)->select();
			}else{
				$picture = M('picture_list')->where(array('type_id'=>$v['tid']))->limit(9)->select();
				$adver   = M('adver')->alias('a')->join('__ADVER_TYPE__ b ON a.adver_type_id = b.adver_type_id AND b.adver_type_name = "首页瀑布流广告"')->limit($k*2,2)->select();
			}
			$att = array();
				$one['picture'] = array_slice($picture, 0,2);
				$a['picture']   = array();
				foreach ($one['picture'] as $key => $value) {
					$arr                    = array();
					$arr['title']           = $value['title'];
					$arr['img_src']         = $value['img_src'];
					$arr['picture_list_id'] = $value['picture_list_id'];
					$a['picture'][]         = $arr;
				}
				$one['adver'] = $adver[0];
				$a['type']      = $k+1;
				$a['adver']   = $one['adver'];
				
				$two['picture'] = array_slice($picture, 2,5);	
				$b['type']      = $k+1;
				$b['picture']   = array();
				foreach ($two['picture'] as $key => $value) {
					$arr                    = array();
					$arr['title']           = $value['title'];
					$arr['img_src']         = $value['img_src'];
					$arr['picture_list_id'] = $value['picture_list_id'];
					$b['picture'][]         = $arr;
				}
				
				$three['adver'] = $adver[1];
				$c['adver']     = $three['adver'];
				$c['type']      = $k+1;
				$three['picture'] = array_slice($picture, 7,2);
				$c['picture']     = array();
				foreach ($three['picture'] as $key => $value) {
					$arr                    = array();
					$arr['title']           = $value['title'];
					$arr['img_src']         = $value['img_src'];
					$arr['picture_list_id'] = $value['picture_list_id'];
					$c['picture'][]         = $arr;
				}
				$att[]  = $a;
				$att[]  = $b;
				$att[]  = $c;
				$temp[] = $att;
		}
		$this->assign('temp',$temp);
		$type = M('type')->select();
		$this->assign('type',$type);
		$this->display();
	}
	/**
	 * 创建更换图片的方法
	 */
	public function edit_list(){
		if(IS_POST){
			$data = I('post.');
			if($data['adver_id']){
				$re = M('adver')->where(array('adver_id'=>$data['adver_id']))->save(array('pic_src'=>$data['img_src']));
			}else{
				$re = M('picture_list')->where(array('picture_list_id'=>$data['picture_list_id']))->save(array('img_src'=>$data['img_src'],'title'=>$data['title']));
			}
			if($re){
				echo '<script type="text/javascript">alert("编辑成功");location.href = "'.U('admin/picture/index_list').'"</script>';
			}else{
				echo '<script type="text/javascript">alert("编辑失败，请重新编辑");</script>';
			}
		}
		$get = I('get.');
		if($get['adver_id']){
			$data = M('adver')->where(array('adver_id'=>$get['adver_id']))->find();
		}else{
			$data = M('picture_list')->where(array('picture_list_id'=>$get['picture_list_id']))->find();
		}
		$this->assign('data',$data);
		$this->display();
	}
	/**
	 * 
	 */
	public function choice(){
		if(IS_POST){
			$data = I('post.');
			$re = M('picture_list')->where(array('picture_list_id'=>$data['picture_list_id']))->save(array('picture_id'=>$data['li']));
			if($re){
				echo '<script type="text/javascript">alert("选取成功");location.href = "'.U('admin/picture/index_list').'"</script>';
			}else{
				echo '<script type="text/javascript">alert("选取失败，请重新选取");</script>';
			}
		}
		$get = I('get.');
		$in = M('picture_list')->getField('picture_id',true);
		$id = implode(',', $in);
		$where['p_id'] = array('not in',$id);
		$where['type_tid'] = $get['type'];
		$data = M('picture')->where($where)->select();
		$att = array();
		foreach ($data as $key => $value){
			$arr = array();
			$arr['box'] = $value;
			$arr['box']['url'] = explode('|',$value['url']);
			$arr['box']['addtime'] = date('Y-m-d H:i:s',$value['addtime']);
			$att[] = $arr;
		}
//		dump($att);die;
		$this->assign('att',$att);
		$this->display();
	}
}