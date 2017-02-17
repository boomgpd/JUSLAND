<?php
namespace Admin\Model;
use Think\Model;

class Shop_taobao_orderModel extends Model{
	protected $tableName = 'shop_taobao_order';
	protected $_validata = array(
		array('taobao_order_num','require','淘宝订单号必须填写！'),
		array('postage','require','邮费必须填写！'),
	);
	
	
	/**
	 * 创建添加淘宝订单号方法
	 */
	public function store($data){
		M()->startTrans();
		if(!$this->create($data)) return FALSE;
		$re = $this->add($data);
		foreach($data['olid'] as $k=>$v){  
			$result = M('shop_orders_list')->where(array('olid'=>$v))->save(array('tb_id'=>$re,'tb_status'=>1));
			if(!$result){
				M()->rollback();
			}
		}
		if(!isset($data['olid'])){
			M()->rollback();
			$this->error = '请勾选对应货品';
			return FALSE;
		}
		$orders_oid = M('shop_orders_list')->where(array('olid'=>$data['olid'][0]))->getField('orders_oid');
		$orders_list_num = M('shop_orders_list')->where(array('orders_oid'=>$orders_oid))->count();
		$orders_list_deal_num = M('shop_orders_list')->where(array('orders_oid'=>$orders_oid,'tb_status'=>1))->count();
		$postage = $this->where(array('oid'=>$orders_oid))->sum('postage');
		if($orders_list_num == $orders_list_deal_num && $orders_list_num != 0){
			M('shop_orders')->where(array('oid'=>$orders_oid,'is_del'=>0))->save(array('tb_status'=>1));
			$order_result = M('shop_orders')->where(array('oid'=>$orders_oid))->setInc('total',$postage);
		}
		M()->commit();
		return $re;		
	}
	
	/**
	 * 创建快递单号添加方法
	 */
	public function ems_add($data){
		$this->_validata = array(
			array('tb_order_id','require','订单主键必须存在！'),
			array('ems_type_id','require','快递类型必须选择！'),
			array('ems_number','require','快递单号必须填写！'),
		);
		$data['sendtime'] = time();
		if(!$this->create($data)) return FALSE;
		M()->startTrans();
		$order_list_re = M('shop_orders_list')->where(array('tb_id'=>$data['tb_order_id']))->setField(array('tb_status'=>2,'statuss'=>'2'));
		if(!$order_list_re){
			$this->error = '订单列表状态更新失败';
			M()->rollback();
			return FALSE;
		}
		$re = $this->where(array('tb_order_id'=>$data['tb_order_id']))->save($this->data);
		if(!$re){
			$this->error = '淘宝订单状态更新失败';
			M()->rollback();
			return FALSE;
		}
		$orders_oid = M('shop_orders_list')->where(array('tb_id'=>$data['tb_order_id']))->getField('orders_oid');
		$orders_list_num = M('shop_orders_list')->where(array('orders_oid'=>$orders_oid))->count();
		$orders_list_deal_num = M('shop_orders_list')->where(array('orders_oid'=>$orders_oid,'tb_status'=>2))->count();
		if($orders_list_num == $orders_list_deal_num && $orders_list_num != 0){
			M('shop_orders')->where(array('oid'=>$orders_oid,'is_del'=>0))->save(array('tb_status'=>2,'status'=>2));
		}
		M()->commit();
		return $re;
	}

}
	