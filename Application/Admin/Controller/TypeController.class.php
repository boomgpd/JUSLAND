<?php namespace Admin\Controller;
use Think\Controller;
class TypeController extends CommonController {
	protected $type;
	
	public function __construct(){
		parent::__construct();
		$this->type = M('type');
	}
	public function addTag(){
		$this->display();
	}
	public function checkTag(){
		$data = I('post.');
		$data = explode('|', $data['tname']);
		$data_name = explode('|', $data['English_name']);
		foreach($data as $k=>$v){
			$arr = array('tname'=>$v);
			$arr = array('English_name'=>$data_name[$k]);
			$this->type->add($arr);
		}
		echo '<script type="text/javascript">alert("添加成功");history.back(-1)</script>';
	}
	public function tag(){
		$condition = array('is_del'=>0);
		if($_POST['search'] == 'not'){
			$condition['is_suggest'] = 0;
		}else if($_POST['search'] == 'yes'){
			$condition['is_suggest'] = 1;
		};
		$info=$this->type->where($condition)->select();
		$this->assign('info',$info);
		$this->display();
	}

	//推荐和取消推荐
	public function suggest($id=NULL){
		if(empty($id)) return FALSE;
		$result = $this->type->where(array('tid'=>$id,'is_del'=>0))->getField('is_suggest');
		if($result){
			$this->type->where(array('tid'=>$id,'is_del'=>0))->save(array('is_suggest'=>0));
		}else{
			$num = $this->type->where(array('is_suggest'=>1,'is_del'=>0))->count();
			if($num >= 4){
				$this->error('推荐失败，最多只能推荐四条',U('Type/tag'));
			};
			$this->type->where(array('tid'=>$id,'is_del'=>0))->save(array('is_suggest'=>1));
		};
		redirect(U('Type/tag'));
	}

	public function savetag(){
		if(!IS_AJAX)	return FALSE;
		$data = I('post.');
		$re = $this->type->save($data);
		if(!$re){
			echo '编辑失败';
		}else{
			echo '编辑成功';
		}
	}
	//创建删除方法
	public function deletetag(){
		if(!IS_AJAX)  return FALSE;
			$data['tid'] = I('post.tid');
			$re = $this->type->where(array('tid'=>$data['tid']))->delete();
			if(!$re){
				echo '删除失败';
			}else{
				echo '删除成功';
			}
	}
}