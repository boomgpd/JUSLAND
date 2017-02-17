<?php
namespace Admin\Model;
use Think\Model;

class EmsModel extends Model{
	
	protected $tableName = 'Shop_ems_type';
	
	protected $_validate = array(
	    array('name','require','请填写代码名',1), //默认情况下用正则进行验证
	    array('title','require','请填写中文名',1), //默认情况下用正则进行验证
	    array('id','require','数据异常',1,'',2), //默认情况下用正则进行验证
	);


	//添加
	public function store($post){
		if(!$this->create($post)) return FALSE;
		$re = $this->where(array('name'=>$post['name'],'is_del'=>0))->find();
		if(!is_null($re)){
			$this->error = '已有一样的代码名';
			return FALSE;
		};
		$re = $this->where(array('title'=>$post['title'],'is_del'=>0))->find();
		if(!is_null($re)){
			$this->error = '已有一样的中文名';
			return FALSE;
		};
		$post['admin_id'] = $_SESSION['admin_id'];
		$post['addtime'] = time();
		return $this->add($post);
	}

	//编辑
	public function edit($post){
		if(!$this->create($post)) return FALSE;
		$re = $this->where(array('name'=>$post['name'],'id'=>array('NEQ',$post['id']),'is_del'=>0))->find();
		if(!is_null($re)){
			$this->error = '已有一样的代码名';
			return FALSE;
		};
		$re = $this->where(array('title'=>$post['title'],'id'=>array('NEQ',$post['id']),'is_del'=>0))->find();
		if(!is_null($re)){
			$this->error = '已有一样的中文名';
			return FALSE;
		};
		$this->save($post);
		return true;
	}

}
?>