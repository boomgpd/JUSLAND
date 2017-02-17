<?php namespace Common\Model;
use Think\Model;

class ShopDetailsGoodsModel extends Model{
	protected $tableName="shop_goods_details";
	protected $_validate = array(
	    array('big','require','图册大图必须存在必须填写！',1), //默认情况下用正则进行验证
	    array('medium','require','图册小图必须存在必须填写！',1), 
	    array('details','require','商品详情必须填写！',1), 
	    array('goods_gid','require','商品主键必须填写！',1), 
   );
	/**
	 * 创建添加方法
	 */	
	public function store($data){
		if(!$this->create($data)) return FALSE;
		$re = $this->add($this->data);
		return $re;
	}
	
	
	/**
	 * 创建编辑方法
	 */	
	public function edit($data){
		if(!$this->create($data)) return FALSE;
		$re = $this->where(array('goods_gid'=>$data['goods_gid'],'is_del'=>0))->save($this->data);
		return $re;
	}
	
}
