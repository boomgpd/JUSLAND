<?php
namespace Home\Controller;
use Think\Controller;
use Common\Model\BananerModel;
use Common\Model\Photo_categoryModel;
use Common\Model\PhotorecomModel;
use Common\Model\PhotoMakeModel;
/**
 * 影楼首页控制器
 * geyu 
 * 11/4
 */
class PhotoController extends CommonController {
	protected $bananer;
	protected $photo;
	protected $make;

	public function __construct() {
		parent::__construct();
		$this -> bananer = new BananerModel;
		$this -> photo = new Photo_categoryModel;
		$this -> photorecom = new PhotorecomModel;
		$this -> make = new PhotoMakeModel;
	}
	/**
	 * 显示首页的方法
	 */
	public function index(){
		$data['bananer'] = $this->bananer->bananer_show('影楼顶部轮播');
//		dump($data);die;
		$data['photo_type'] = $this->photo->getData();
		$data['photo_c'] = $this->photorecom->getData();
		$data['photo_tnt'] = $this->photorecom->time_limit();
		$data['photo_sell'] = $this->photorecom->sell();
		$this->assign('data',$data);
		$this->display();
	}
	/**
	 *显示列表页的方法
	 */ 
	public function photo_list(){
		if(IS_POST){
			$data = I('post.');
			$data['photo_goods'] = $this->photo->list_goods($data);
		}
		$get = I('get.');
		if($get['desc']){
			$arr = explode('_',$get['desc']);
			$this->assign('arr',$arr);
		}else{
			$arr = array_fill(0, 3, 0);	
			$this->assign('arr',$arr);
		}
		$data['photo_list'] = $this->photo->getList($get['id']);
		$data['photo_goods'] = $this->photo->list_goods($get);
//		dump($data['photo_goods']);die;
		$data['area'] = M('area')->where(array('pid'=>$_SESSION['location_city_id']))->select();
		$this->assign('data',$data);
		$this->display();
	}
	
	//产品详情页
	public function details($id=NULL){
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
		if(empty($id)) return false;
		$data = M('Photo_product')->where(array('p_p_id'=>$id,'is_del'=>0))->find();
		$info = M('Photo_product')->where(array('p_p_id'=>$id))->setInc('moods');
		if(is_null($data)){
			alert('页面丢失',U('Home/Index/index'));die;
		};
		$data['picture'] = explode(',',$data['picture']);
		$data['clogo'] = M('Photo_category')->where(array('is_del'=>0,'p_c_id'=>$data['p_c_id']))->getField('clogo');
		$tab = M('Photo_product_tab')->where(array('p_p_id'=>$id,'is_del'=>0))->select();
		foreach($tab as $k => &$v){
			$v['p_a_name'] = M('photo_name')->where(array('photo_name_id'=>$v['p_a_name']))->getField('name');
			$data['tab'][$v['p_a_type']][] = $v;
		};
		$this->assign('data',$data);
		$this->display();
	}
}