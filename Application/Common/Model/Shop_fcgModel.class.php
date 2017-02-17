<?php
namespace Common\Model;
use Think\Model;

class Shop_fcgModel extends Model{
	
	protected $tableName = 'shop_fc_goods';
	
	protected $_validate = array(
		array('one','require','请填写第一句',1),
		array('two','require','请填写第二句',1),
		array('img','require','请选择图片',1),
		array('sort','require','数据异常',1,'',1),
		array('fcid','require','数据异常',1),
		array('gid','require','数据异常',1),
	);

	//添加
	public function store($post){
		if(!$this->create($post)) return FALSE;
		$num = $this->where(array('fcid'=>$post['fcid'],'is_del'=>0))->count();
		if($num >= 8){
			$this->error = '最多只能添加八个';
			return FALSE;
		};
		$re = $this->where(array('fcid'=>$post['fcid'],'gid'=>$post['gid'],'is_del'=>0))->find();
		if(!is_null($re)){
			$this->error = '该商品已在此推荐中';
			return FALSE;
		};
		$re = $this->where(array('fcid'=>$post['fcid'],'sort'=>$post['sort'],'is_del'=>0))->find();
		if(!is_null($re)){
			$this->error = '该分类下已有此排序的推荐';
			return FALSE;
		};
		$post['addtime'] = time();
		$post['admin_id'] = $_SESSION['admin_id'];
		return $this->add($post);
	}

	//编辑
	public function edit($post){
		if(!$this->create($post)) return FALSE;
		$this->where(array('fcid'=>$post['fcid'],'gid'=>$post['gid'],'is_del'=>0))->save($post);
		return true;
	}
}
?>