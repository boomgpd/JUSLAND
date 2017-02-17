<?php namespace Admin\Controller;
use Think\Controller;
use Admin\Model\Mer_nodeModel;
/**
 * 创建商家权限控制器
 * 16/11/08
 * 谭超
 */
class MerpowerController extends CommonController {

	private $node;
	
	/**
	 * 创建构造函数
	 */
	 public function __construct(){
	 	$this->node = new Mer_nodeModel;
	 	parent::__construct();
	 }

	 //系统列表
	 public function sys_list(){
	 	$data = $this->node->where(array('is_del'=>0,'level'=>1))->select();
	 	$this->assign('data',$data);
	 	$this->display();
	 }

	 //添加系统
	 public function sys_add(){
	 	if(IS_POST){
			$post = I('post.');
			$re = $this->node->store($post,1,0);
			if(!$re){
				alert($this->node->getError());die;
			};
			alert('添加成功',U('Merpower/sys_list'));die;
		};
	 	$this->display();
	 }

	//编辑系统
	public function sys_edit($id=NULL){
		if(empty($id)) return FALSE;
		if(IS_POST){
			$post = I('post.');
			$re = $this->node->edit($post,1,0);
			if(!$re){
				alert($this->node->getError());die;
			};
			alert('编辑成功',U('Merpower/sys_list'));die;
		};
		$data = $this->node->where(array('is_del'=>0,'id'=>$id))->find();
		$this->assign('data',$data);
		$this->display();
	}


	//模块列表
	public function module_list($id=NULL){
		if(empty($id)) return FALSE;
		$data = $this->node->where(array('pid'=>$id,'is_del'=>0))->select();
		$this->assign('data',$data);
		$this->assign('id',$id);
		$this->display();
	}

	//添加模块
	public function module_add($id=NULL){
		if(empty($id)) return FALSE;
		if(IS_POST){
			$post = I('post.');
			$re = $this->node->store($post,2,$id);
			if(!$re){
				alert($this->node->getError());die;
			};
			alert('添加成功',U('Merpower/module_list',array('id'=>$id)));die;
		};
		$this->display();
	}

	//编辑模块
	public function module_edit($id=NULL){
		if(empty($id)) return FALSE;
		if(IS_POST){
			$post = I('post.');
			$re = $this->node->edit($post);
			if(!$re){
				alert($this->node->getError());die;
			};
			alert('编辑成功',U('Merpower/module_list',array('id'=>$re['pid'])));die;
		};
		$data = $this->node->where(array('id'=>$id,'is_del'=>0))->find();
		$this->assign('data',$data);
		$this->display();
	}


	//控制器列表
	public function con_list($id=NULL){
		if(empty($id)) return FALSE;
		$data = $this->node->where(array('is_del'=>0,'pid'=>$id))->select();
		$this->assign('data',$data);
		$this->assign('id',$id);
		$this->display();
	}

	//添加控制器
	public function con_add($id=NULL){
		if(empty($id)) return FALSE;
		if(IS_POST){
			$post = I('post.');
			$re = $this->node->store($post,3,$id);
			if(!$re){
				alert($this->node->getError());die;
			};
			alert('添加成功',U('con_list',array('id'=>$id)));die;
		};
		$this->display();
	}

	//编辑控制器
	public function con_edit($id=NULL){
		if(empty($id)) return FALSE;
		if(IS_POST){
			$post = I('post.');
			$re = $this->node->edit($post);
			if(!$re){
				alert($this->node->getError());die;
			};
			alert('编辑成功',U('con_list',array('id'=>$re['pid'])));die;
		};
		$data = $this->node->where(array('id'=>$id,'is_del'=>0))->find();
		$this->assign('data',$data);
		$this->display();
	}


	//方法列表
	public function act_list($id=NULL){
		if(empty($id)) return FALSE;
		$data = $this->node->where(array('is_del'=>0,'pid'=>$id))->select();
		$this->assign('id',$id);
		$this->assign('data',$data);
		$this->display();
	}

	//添加方法
	public function act_add($id=NULL){
		if(empty($id)) return FALSE;
		if(IS_POST){
			$post = I('post.');
			$re = $this->node->store($post,4,$id);
			if(!$re){
				alert($this->node->getError());die;
			};
			alert('添加成功',U('act_list',array('id'=>$id)));die;
		};
		$this->display();
	}

	//编辑方法
	public function act_edit($id=NULL){
		if(empty($id)) return FALSE;
		if(IS_POST){
			$post = I('post.');
			$re = $this->node->edit($post);
			if(!$re){
				alert($this->node->getError());die;
			};
			alert('编辑成功',U('con_list',array('id'=>$re['pid'])));die;
		};
		$data = $this->node->where(array('id'=>$id,'is_del'=>0))->find();
		$this->assign('data',$data);
		$this->display();
	}
}