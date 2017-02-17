<?php
namespace Common\Model;
use Think\Model;

class Shop_type_attrModel extends Model{
	
	protected $tableName = 'shop_type_attr';
	
	protected $_validate = array(
		array('taname','require','属性名不能为空',1),
		array('tavalue','require','属性值不能为空',1),
		array('class','require','类别不能为空',1),
		array('type_id','require','数据异常',1,'',1),
		array('taid','require','数据异常',1,'',2),
	);

	//添加
	public function store($post){
		if(!$this->create($post)) return FALSE;
		$re = $this->where(array('type_id'=>$post['type_id'],'taname'=>$post['taname'],'is_del'=>0))->find();
		if(!is_null($re)){
			$this->error = '该类型下已有一样的属性名';
			return FALSE;
		};
		$post['admin_id'] = $_SESSION['admin_id'];
		$post['addtime'] = time();
		return $this->add($post);
	}

	//编辑
	public function edit($post){
		if(!$this->create($post)) return FALSE;
		$old = $this->where(array('taid'=>$post['taid'],'is_del'=>0))->find();
		if(is_null($old)){
			$this->error = '数据异常';
			return FALSE;
		};
		$re = $this->where(array('type_id'=>$old['type_id'],'taname'=>$post['taname'],'taid'=>array('NEQ',$post['taid']),'is_del'=>0))->find();
		if(!is_null($re)){
			$this->error = '已有一样的属性名';
			return FALSE;
		};
		$this->save($post);
		$data = $this->where(array('taid'=>$post['taid'],'is_del'=>0))->find();
		if(is_null($data)){
			$this->error = '数据异常';
			return FALSE;
		};
		return $data;
	}

}
?>