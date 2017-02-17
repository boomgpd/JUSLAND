<?php namespace Hotel\Controller;
use Think\Controller;
use Hotel\Model\HotelModel;

/**
 * 影楼管理基本控制器
 * 谭超
 * 16/11/5
 */
Class BaseController extends CommonController {

	private $hotel;
	
	public function __construct(){
		parent::__construct();
		$this->hotel = new HotelModel;

	}
	
	//个人信息
	public function info(){
//		dump($_SESSION);die;
		if(IS_POST){
			$data = I('post.');
			$this->hotel->where(array('is_del'=>0,'apply_type'=>1,'hotel_id'=>$_SESSION['hotel_id']))->save($data);
			alert('编辑成功',U('Index/index'),true);die;
		};
		$data = $this->hotel->where(array('is_del'=>0,'apply_type'=>1,'hotel_id'=>$_SESSION['hotel_id']))->find();
		$this->assign('data',$data);
		$area = M('Area')->where(array('type'=>1))->select();
		$city = M('Area')->where(array('type'=>2,'pid'=>$data['provience']))->select();
		$this->assign('city',$city);
		$this->assign('area',$area);
		$this->display();
	}
}