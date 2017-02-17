<?php
namespace Home\Controller;
use Think\Controller;
use Common\Model\Hotel_categoryModel;
use Common\Model\BananerModel;
use Common\Model\HotelrecomModel;
use Common\Model\HotelMakeModel;
/**
 * geyu
 * 11.12
 * 创建酒店控制器犯法
 */
class HotelController extends CommonController {
	protected $hotel_category;
	protected $recom;
	protected $bananer;
	protected $make;
	public function __construct() {
		parent::__construct();
		$this -> bananer = new BananerModel;
		$this -> hotel_category = new Hotel_categoryModel;
		$this -> recom = new HotelrecomModel;
		$this -> make = new HotelMakeModel;
	}
	/**
	 * 酒店首页
	 */
	public function index(){
		$data['bananer'] = $this->bananer->bananer_show('酒店顶部轮播');
		$data['hotel'] = $this->recom->getData();
		$this->assign('data',$data);
		$this->display();
	}
	/** 
	 * 酒店列表页
	 */
	public function hotel_list(){
		if($_GET['desc']){
			$arr = explode('_', $_GET['desc']);
			$this->assign('arr',$arr);
		}else{
			$arr = array_fill(0, 4, 0);
			$this->assign('arr',$arr);
		}
		$data['goods'] = $this->hotel_category->goods($_GET);
		$data['hotel_type'] = $this->hotel_category->getData();
		$data['area'] = M('area')->where(array('pid'=>$_SESSION['location_city_id']))->select();
		$this->assign('data',$data);
		$this->display();
	}
	/**
	 * 酒店详情页
	 */
	public function details(){
		if(IS_POST){
			$data = I('post.');
			$re = $this->make->make($data);
			if($re['info']){
				$res = M('Photo_product')->where(array('p_p_id'=>$data['p_p_id']))->setInc('make_num');
				alert("您预约以成功，工作人员会在24小时内联系您，请您的手机保持畅通！",U('details',array('id'=>$data['p_p_id'])));die;
			}else if($re['bool']){
				alert($re['bool'],U('details',array('id'=>$data['p_p_id'])));die;
			}else{
				alert($this->make->getError(),U('details',array('id'=>$data['p_p_id'])));
			}
		}
		$get = I('get.id');
		$data = M('hotel_product')->where(array('is_del'=>0,'p_p_id'=>$get))->find();
		$data['picture'] = explode(',', $data['picture']);
		$data['p_area'] = implode(" ", explode("|", $data['p_area']));
		$data['h_show'] = explode('|', $data['h_show']);;
		$this->assign('data',$data);
		// dump($data);
		$root = M('hotel')->where(array('hotel_id'=>$data['hotel_id']))->find();
		$this->assign('root',$root);
		// dump($root);die;
		$area['shen'] = M('area')->where(array('area_id'=>$root['provience']))->getField('aname');
		$area['city'] = M('area')->where(array('area_id'=>$root['city']))->getField('aname');
		$this->assign('area',$area);
		
		$type = M('hotel_category')->where(array('p_c_id'=>$data['p_c_id']))->find();
		$this->assign('type',$type);
		
		$tad = M('hotel_name')->where(array('is_del'=>0))->select();
		foreach ($tad as $key => $value) {
			$att[] = M('hotel_value')->where(array('p_p_id'=>$get,'p_name_id'=>$value['p_p_a_id']))->find();
			$add[] = $value;
		}
		// dump($add);
		// dump($att);die;
		$this->assign('att',$att);
		$this->assign('add',$add);
		$this->display();
	}
	/**
	 * 对比页面
	 */
	public function contrast(){
		if(!$_SESSION['ids']) return FALSE;
		$basic['bas'] = M('hotel_name')->field('p_p_a_id,p_a_name')->where(array('is_del'=>0,'level'=>1))->select();
		foreach ($basic['bas'] as $k => &$v) {
			foreach ($_SESSION['ids'] as $key => $value) {
				$v['value'][] = M('hotel_value')->where(array('p_name_id'=>$v['p_p_a_id'],'p_p_id'=>$value))->getField('hotel_value');
			}
		}
		$basic['fav'] = M('hotel_name')->field('p_p_a_id,p_a_name')->where(array('is_del'=>0,'level'=>2))->select();
		foreach ($basic['fav'] as $k => &$v) {
			foreach ($_SESSION['ids'] as $key => $value) {
				$v['value'][] = M('hotel_value')->where(array('p_name_id'=>$v['p_p_a_id'],'p_p_id'=>$value))->getField('hotel_value');
			}
		}
		$basic['req'] = M('hotel_name')->field('p_p_a_id,p_a_name')->where(array('is_del'=>0,'level'=>3))->select();
		foreach ($basic['req'] as $k => &$v) {
			foreach ($_SESSION['ids'] as $key => $value) {
				$v['value'][] = M('hotel_value')->where(array('p_name_id'=>$v['p_p_a_id'],'p_p_id'=>$value))->getField('hotel_value');
			}
		}
		$basic['reqs'] = M('hotel_name')->field('p_p_a_id,p_a_name')->where(array('is_del'=>0,'level'=>4))->select();
		foreach ($basic['reqs'] as $k => &$v) {
			foreach ($_SESSION['ids'] as $key => $value) {
				$v['value'][] = M('hotel_value')->where(array('p_name_id'=>$v['p_p_a_id'],'p_p_id'=>$value))->getField('hotel_value');
			}
		}
		$att = array();
		foreach ($_SESSION['ids'] as $key => $value) {
			$arr =  M('hotel_product')->field('list_img,p_p_id,hotel_id')->where(array('p_p_id'=>$value))->find();
			$arr['hotel_id'] = M('hotel')->where(array('hotel_id'=>$arr['hotel_id']))->getField('h_name');
			$att[] = $arr;
		}
		$this->assign('att',$att);
		$this->assign('basic',$basic);
		$this->display();
	}
	/**
	 * 处理对比的方法
	 */
	public function check(){
		if(!IS_AJAX) return FALSE;
		$data = I('post.');
		if($_SESSION['ids']){
			$arr = $_SESSION['ids'];
		}else{
			$arr = array();
		}
		if($data['action'] == 'add'){
			$arr[] = $data['ids'];
		}else if($data['action'] == 'del'){
			foreach ($arr as $key => $value) {
				if($value == $data['ids']){
					unset($arr[$key]);
				}
			}
		}
		if(count($arr) > 3){
			$arr = array('type'=>0,'conent'=>"最多三个");
		}else{
			session('ids',$arr);
			$arr = array('type'=>1,'conent'=>count($_SESSION['ids']));
		}
		$this->ajaxReturn($arr);exit;
	}
	/**
	 * 清除session下面的ids
	 */
	public function conget(){
		if(!IS_GET) return FALSE;
		$get = I('get.id');
		foreach ($_SESSION['ids'] as $key => $value) {
			if($value == $get){
				unset($_SESSION['ids'][$key]);
			}
		}
		redirect(U('contrast'));
	}
}