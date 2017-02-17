<?php namespace Admin\Controller;
header('Content-Type: text/html; charset=utf-8');
use Think\Controller;
use Common\Model\HotelrecomModel;

/**
 * 创建酒店推荐的控制器
 * 16/11/12
 * 葛羽
 */
class HotelrecomController extends CommonController {
	protected $recom;
	
	public function __construct(){
		parent::__construct();
		$this->recom = new HotelrecomModel;
	}
	/**
	 * 创建首页推荐
	 */
	public function index_class(){
		$data = M('hotel_recom')->where(array('is_del'=>0))->select();
		foreach ($data as $key => &$value) {
			$value['p_p_id'] = M('hotel_product')->where(array('p_p_id'=>$value['p_p_id']))->getField('p_name');
		}
		$this->assign('data',$data);
//		dump($data);die;
		$this->display();
	}
	
	
	/**
	 * 推荐商品页面方法
	 */
	public function add_class(){
		if(IS_POST){
			$data = I('post.');
			$re = $this->recom->add($data);
			if($re){
				alert('推荐成功',U('index_class'));
			}else{
				alert($this->recom->getEroor(),U('add_class'));
			}
		}
		$re  = M("hotel_recom")->where(array('is_del'=>0))->getField('p_p_id',TRUE);
		if(!$re){
		}else{
			$id = implode(',', $re);
			$where['p_p_id'] = array('not in' ,$id);
		}
		$where['is_del'] = 0;
		$data = M('hotel_product')->where($where)->select();
		foreach ($data as $key => &$value) {
			$value['hotel_id'] = M('hotel')->where(array('hotel_id'=>$value['hotel_id']))->getField('h_name'); 
		}
		$this->assign('data',$data);
		$this->display();
	}


	/**
	 * 首推方法
	 */
	public function edit_class(){
		if(IS_POST){
			$data = I('post.');
			$re = M('hotel_recom')->where(array('p_r_t_id'=>$data['id']))->save(array('list_src'=>$data['list_src'],'p_p_id'=>$data['p_p_id'],'p_r_t_title'=>$data['p_r_t_title']));
			if($re){
				alert('编辑成功',U('index_class'));
			}else{
				alert('编辑失败',U('edit_class'));
			}
		}
		$get = I('get.id');
		$re  = M("hotel_recom")->where(array('is_del'=>0))->getField('p_p_id',TRUE);
		if(!$re){
		}else{
			$id = implode(',', $re);
			$where['p_p_id'] = array('not in' ,$id);
		}
		$where['is_del'] = 0;
		$data = M('hotel_product')->where($where)->select();
		foreach ($data as $key => &$value) {
			$value['hotel_id'] = M('hotel')->where(array('hotel_id'=>$value['hotel_id']))->getField('h_name'); 
		}
		$this->assign('data',$data);
		$arr = M('hotel_recom')->where(array('p_r_t_id'=>$get))->find();
		$this->assign('arr',$arr);
		$this->display();
	}
	
	
	/**
	 * 下线方法
	 */
	public function del_class(){
		if(IS_AJAX){
			$data = I('post.del');
			$re = M('hotel_recom')->where(array('p_r_t_id'=>$data))->save(array('is_del'=>1));
			$this->ajaxReturn($re);exit;
		}
	}
}