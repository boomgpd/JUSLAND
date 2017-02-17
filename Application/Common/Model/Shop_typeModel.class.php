<?php
namespace Common\Model;
use Think\Model;

class Shop_typeModel extends Model{
	
	protected $tableName = 'shop_type';
	
	protected $_validate = array(
		array('tname','require','类型名不能为空',1),
		array('tname','','已有一样类型名',1,'unique',1),
		array('tid','require','数据异常',1,'',2),
	);

	//添加
	public function store($post){
		if(!$this->create($post)) return FALSE;
		$post['admin_id'] = $_SESSION['admin_id'];
		$post['addtime'] = time();
		return $this->add($post);
	}

	//编辑
	public function edit($post){
		if(!$this->create($post)) return FALSE;
		$re = $this->where(array('tname'=>$post['tname'],'tid'=>array('NEQ',$post['tid']),'is_del'=>0))->find();
		if(!is_null($re)){
			$this->error = '已有相同的类型名';
			return FALSE;
		};
		$this->save($post);
		return true;
	}

}
?>