<?php namespace Photo\Controller;
use Think\Controller;
use Photo\Model\PhotoModel;

/**
 * 影楼管理基本控制器
 * 谭超
 * 16/11/5
 */
Class BaseController extends CommonController {

	private $photo;
	
	public function __construct(){
		parent::__construct();
		$this->photo = new PhotoModel;

	}
	
	//个人信息
	public function info(){
		if(IS_POST){
			$data = I('post.');
			$this->photo->where(array('is_del'=>0,'apply_type'=>1,'photo_id'=>$_SESSION['photo_id']))->save($data);
			alert('编辑成功',U('Index/index'),true);die;
		};
		$data = $this->photo->where(array('is_del'=>0,'apply_type'=>1,'photo_id'=>$_SESSION['photo_id']))->find();
		$this->assign('data',$data);
		$area = M('Area')->where(array('type'=>1))->select();
		$city = M('Area')->where(array('type'=>2,'pid'=>$data['provience']))->select();
		$this->assign('city',$city);
		$this->assign('area',$area);
		$this->display();
	}
}