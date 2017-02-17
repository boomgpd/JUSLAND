<?php
namespace Photo\Controller;
use Photo\Model\ProductModel;
use Photo\Model\OddsModel;
/*
* 影楼商品控制器
* 谭超
* 16/11/5
*/
class ProductController extends CommonController {

	private $db;
	private $odds;

	public function __construct(){
		parent::__construct();
		$this->db = new ProductModel;
		$this->odds = new OddsModel;
	}

	//首页
	public function index(){
		$data = $this->db->where(array('is_del'=>0,'photo_id'=>$_SESSION['photo_id']))->select();
		$this->assign('data',$data);
		$this->display();
	}

	//编辑
	public function edit($id=NULL){
		$id = isset($_POST['p_p_id'])?$_POST['p_p_id']:$id;
		$pids = M('Photo_product')->where(array('is_del'=>0,'photo_id'=>$_SESSION['photo_id']))->getField('p_p_id',true);
		if(empty($id) || !in_array($id,$pids)){
			alert('非法请求',U('Index/index'),true);die;
		};
		if(IS_POST){
			$post = I('post.');
			$re = $this->db->edit($post);
			if(!$re){
				alert($this->db->getError());die;
			};
			alert('编辑成功',U('Product/index'));die;
		};
		//获得分类数据
		$category = M('Photo_category')->where(array('is_del'=>0,'level'=>3))->select();
		$this->assign('category',$category);
		$data = $this->db->getData($id);//获得编辑数据
		$this->assign('data',$data);
		$this->display();
	}


	//添加
	public function add(){
		if(IS_POST){
			$post = I('post.');
			//执行添加
			$re = $this->db->store($post);
			if(!$re){
				alert($this->db->getError());die;
			};
			alert('添加成功',U('Product/Index'));die;
		};
		//获得分类数据
		$type_name = M('photo_name')->where(array('is_del'=>0))->select();
		$category = M('Photo_category')->where(array('is_del'=>0,'level'=>3))->select();
		$this->assign('category',$category);
		$this->assign('type_name',$type_name);
		$this->assign('arr',array('1','2','3','4','5'));
		$this->display();
	}

	//删除
	public function del($id=NULL){
		$pids = M('Photo_product')->where(array('is_del'=>0,'photo_id'=>$_SESSION['photo_id']))->getField('p_p_id',true);
		if(empty($id) || !in_array($id,$pids) || !IS_AJAX){
			return $this->ajaxReturn(array('type'=>0,'content'=>'非法请求'));die;
		};
		M()->startTrans();
		$re = M('Photo_product')->where(array('p_p_id'=>$id))->save(array('is_del'=>1));
		if(!$re){
			M()->rollback();
			return $this->ajaxReturn(array('type'=>0,'content'=>'删除失败'));die;
		};
		$re = M('Photo_product_tab')->where(array('p_p_id'=>$id))->save(array('is_del'=>1));
		if(!$re){
			M()->rollback();
			return $this->ajaxReturn(array('type'=>0,'content'=>'删除失败'));die;
		}else{
			M()->commit();
			return $this->ajaxReturn(array('type'=>1,'content'=>'删除成功'));die;
		};
	}

	//特惠列表
	public function odds_list(){
		$condition = array('po.is_del'=>0,'pp.is_del'=>0,'photo_id'=>$_SESSION['photo_id']);
		if(IS_POST){
			$keyword = I('post.keyword');
			if(!empty($keyword)){
				$condition['pp.p_name'] = array('LIKE','%'.$keyword.'%');
			};
		};
		$data = M('Photo_odds po')->where($condition)->join('photo_product pp ON po.p_p_id = pp.p_p_id')->select();
		$this->assign('data',$data);
		$this->display();
	}
	 
	//添加列表
	public function add_index(){
		$pids = M('Photo_product')->where(array('is_del'=>0,'photo_id'=>$_SESSION['photo_id']))->getField('p_p_id',true);
		$poids = M('Photo_odds')->where(array('is_del'=>0,'p_p_id'=>array('IN',implode(',',$pids))))->getField('p_p_id',true);
		foreach($pids as $k => $v){
			if(in_array($v,$poids)){
				unset($pids[$k]);
			};
		};
		$condition = array('is_del'=>0,'p_p_id'=>array('IN',implode(',',$pids)));
		if(IS_POST){
			$keyword = I('post.keyword');
			if(!empty($keyword)){
				$condition['p_name'] = array('LIKE','%'.$keyword.'%');
			};
		};
		$data = M('Photo_product')->where($condition)->select();
		$this->assign('data',$data);
		$this->display();
	}

	//添加特惠
	public function add_odds($id=NULL){
		$re = $this->check_odds($id,true);
		if(!$re){
			alert('非法操作',U('add_index'));die;
		};
		if(IS_POST){
			$post = I('post.');
			$re = $this->odds->store($post,$id);
			if(!$re){
				alert($this->odds->getError());die;
			};
			alert('添加成功',U('add_index'));die;
		};
		$this->display();
	}

	//编辑特惠
	public function edit_odds($id=NULL){
		$re = $this->check_odds($id);
		if(!$re){
			alert('非法操作');die;
		};
		if(IS_POST){
			$post = I('post.');
			$re = $this->odds->edit($post,$id);
			if(!$re){
				alert($this->odds->getError());die;
			};
			alert('编辑成功',U('odds_list'));die;
		};
		$data = $this->odds->getData($id);
		$this->assign('data',$data);
		$this->display();
	}

	//删除特惠
	public function del_odds(){
		if(!IS_AJAX) return FALSE;
		$id = I('post.id');
		$re = $this->check_odds($id);
		if(!$re){
			return $this->ajaxReturn(array('type'=>0,'content'=>'操作失败'));die;
		};
		M('Photo_odds')->where(array('is_del'=>0,'p_p_id'=>$id))->save(array('is_del'=>1));
		return $this->ajaxReturn(array('type'=>1,'content'=>'删除成功'));die;
	}



	//验证特惠的操作
	private function check_odds($id,$action=FALSE){
		if(empty($id)){
			return FALSE;
		};
		$re = M('Photo_product')->where(array('is_del'=>0,'p_p_id'=>$id,'photo_id'=>$_SESSION['photo_id']))->find();
		if(is_null($re)){
			return FALSE;
		};
		$re = M('Photo_odds')->where(array('is_del'=>0,'p_p_id'=>$id))->find();
		if($action === true){
			if(!is_null($re)){
				return FALSE;
			};
		}else{
			if(is_null($re)){
				return FALSE;
			};
		};
		return true;
	}

	//获得对应分类的规格
	public function get_spec(){
		if(!IS_AJAX) return FALSE;
		$cid = I('post.cid');
		$temp = M('Photo_category')->where(array('is_del'=>0,'p_c_id'=>$cid,'level'=>3))->find();
		if(is_null($temp)) return FALSE;
		$data['area'] = explode('|',$temp['p_area_name']);
		$data['type'] = explode('|',$temp['p_type_name']);
		$data['money'] = explode('|',$temp['p_money_name']);
		$this->ajaxReturn($data);die;
	}
}