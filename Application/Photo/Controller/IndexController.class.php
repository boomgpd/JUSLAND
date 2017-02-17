<?php
namespace Photo\Controller;
/*
*影楼商家后台主页控制器
*16/10/27
*谭超
*/

class IndexController extends CommonController {

	public function __construct(){
		parent::__construct();
	}

	
	//首页
	public function index(){
		$data = M('Photo')->where(array('photo_id'=>$_SESSION['photo_id']))->find();
		$this->assign('data',$data);
		$this->display();
	}

	//欢迎
	public function welcome(){
		$this->display();
	}
	 
};