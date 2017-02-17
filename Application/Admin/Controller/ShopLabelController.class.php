<?php namespace Admin\Controller;
use Think\Controller;
use Common\Model\Shop_labelModel;
/**
 * 商品标签控制器
 * 16/12/03
 * 谭超
 */
class ShopLabelController extends CommonController {

	private $db;
	
	/**
	 * 创建构造函数
	 */
	 public function __construct(){
	 	parent::__construct();
	 	$this->db = new Shop_labelModel;
	 }

	//列表页
	public function index(){
		$data = $this->db->alias('l')->where(array('l.is_del'=>0,'c.is_del'=>0))->join('__SHOP_CATEGORY__ c ON l.category_id = c.cid')->select();
		$this->assign('data',$data);
		$this->display();
	}

	//添加
	public function add(){
		if(IS_POST){
			$post = I('post.');
			$re = $this->db->store($post);
			if($re === FALSE){
				alert($this->db->getError());die;
			};
			alert('添加成功',U('index'));die;
		};
		$cate = M('shop_category')->where(array('pid'=>0,'is_del'=>0))->select();
		$this->assign('cate',$cate);
		$this->display();
	}

	//编辑
	public function edit($id=NULL){
		if(is_null($id)) return FALSE;
		if(IS_POST){
			$post = I('post.');
			$re = $this->db->edit($post);
			if($re === FALSE){
				alert($this->db->getError());die;
			};
			alert('编辑成功');die;
		};
		$data = $this->db->alias('l')->where(array('l.id'=>$id,'l.is_del'=>0,'c.is_del'=>0))->join('__SHOP_CATEGORY__ c ON l.category_id = c.cid')->find();
		$this->assign('data',$data);
		$cate = M('shop_category')->where(array('pid'=>0,'is_del'=>0))->select();
		$this->assign('cate',$cate);
		$this->display();
	}
	 
}