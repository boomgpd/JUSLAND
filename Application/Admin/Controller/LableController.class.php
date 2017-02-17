<?php namespace Admin\Controller;
use Think\Controller;
class LableController extends CommonController {
	protected $lable;
	protected $type;
	
	public function __construct(){
		parent::__construct();
		$this->lable = M('lable');
		$this->type = M('type');
	}
	
	public function addTag(){
		$type_tid = I('get.type_tid');
		$this->assign('type_tid',$type_tid);
		$this->display();
	}
	public function checkTag(){
		$data = I('post.lname');
		$type_tid = I('post.type_tid');
		$data  = explode('|', $data);
		foreach($data as $k=>$v){
			$info = $this->lable->add(array('lname'=>$v,'type_tid'=>$type_tid));
		}
		$this->success('添加成功',U('lable/addTag',array('type_tid'=>$type_tid)));
	}
	public function tag(){
		$type_tid = I('get.type_tid');
		$info=$this->lable->where(array('type_tid'=>$type_tid))->select();
		$this->assign('info',$info);
		$this->display();
	}
	public function savetag(){
		if(!IS_AJAX)	return FALSE;
		$data = I('post.');
		$re = $this->lable->save($data);
		if($re){
			echo '编辑成功';
		}else{
			echo '编辑失败';
		}
	}
	
	
	//创建删除方法
	public function deletetag(){
		if(!IS_AJAX)  return FALSE;
			$data['lid'] = I('post.lid');
			$re = $this->lable->where(array('lid'=>$data['lid']))->delete();
			if(!$re){
				echo '删除失败';
			}else{
				echo '删除成功';
			}
		
		
	}
	
}