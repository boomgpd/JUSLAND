<?php
namespace Hotel\Controller;
/*
*酒店商家后台主页控制器
*16/11/12
*葛羽
*/

class IndexController extends CommonController {

	public function __construct(){
		parent::__construct();
	}

	
	//首页
	public function index(){
		$data = M('hotel')->where(array('hotel_id'=>$_SESSION['hotel_id']))->find();
		$this->assign('data',$data);
		$this->display();
	}

	//欢迎
	public function welcome(){
		$this->display();
	}
	 
};