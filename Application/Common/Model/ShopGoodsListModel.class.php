<?php namespace Common\Model;
use Think\Model;

class ShopGoodsListModel extends Model{
	protected $tableName="shop_goods_list";
	protected $_validate = array(
	    array('combine','require','组合id必须存在必须填写！'), 
	    array('goods_gid','require','商品主键必须填写！'), 
	    array('price','require','商城价格必须填写！'), 
	    array('market','require','市场价格必须填写！'), 
   );
	/**
	 * 创建添加方法
	 */	
	public function store($data){
		$goods_attr = new ShopAttrGoodsModel;
		$goods_attr = $goods_attr->store($data);
		$data['combine']  = implode("|", $goods_attr);
		if(!$this->create($data)) return FALSE;
		$result = $this->where(array('combine'=>$data['combine'],'goods_id'=>$data['goods_id'],'is_del'=>0))->find();
		if($result){
			$this->error = '该货品已经存在';
			return FALSE;
		}
		$re = $this->add($this->data);
		return $re;
	}
	
	/**
	 * 创建编辑方法
	 */	
	public function edit($data){
		$data['list']  = $this->where(array('glid'=>$data['glid']))->find();
		$goods_attr = new ShopAttrGoodsModel;
		$goods_attr = $goods_attr->edit($data);
		if(!$goods_attr){
			$this->error = '数据异常';
			return FALSE;
		}
		$data['combine'] = $goods_attr;
		if(!$this->create($data)) return FALSE;
		$result = $this->where(array('combine'=>$data['combine'],'goods_id'=>$data['goods_id'],'is_del'=>0,'glid'=>array('neq',$data['glid'])))->find();
		if($result){
			$this->error = '该货品已经存在';
			return FALSE;
		}
		$re = $this->save($this->data);
		return $re;
	}
	
}
