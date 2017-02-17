<?php
namespace Admin\Model;
use Think\Model;

class Mer_nodeModel extends Model{
	
	protected $tableName = 'mer_node';
	
	protected $_validate = array(
		array('name','require','名称不能为空',1),
		array('title','require','标题不能为空',1),
	);

	//添加
	public function store($post,$level,$pid){
		if(!$this->create($post)) return FALSE;
		$post['addtime'] = time();
		$post['level'] = $level;
		$post['pid'] = $pid;
		return $this->add($post);
	}

	//编辑
	public function edit($post){
		if(!$this->create($post)) return FALSE;
		$this->save();
		return $this->where(array('id'=>$post['id'],'is_del'=>0))->find();
	}
}
?>