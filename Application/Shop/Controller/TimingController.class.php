<?php
namespace Shop\Controller;
use Think\Controller;
header("Content-type: text/html; charset=utf-8"); 
/**
 * 创建定时更新商家订单状态的控制器
 */
class TimingController extends Controller {
	/**
	 * 查看淘宝订单是否逾期收货  并更新状态；
	 */
	 public function index(){
		//先查看已发货的商家订单
	 	$orders = M('shop_orders')->where(array('tb_status'=>2,'status'=>2,'is_del'=>0))->getField('oid',TRUE);
		//然后遍历，在查找快递单号表
		foreach($orders as $k => $v){
			$taobao_order[] = M('shop_taobao_order')->where(array('oid'=>$v))->select();
		}
		foreach($taobao_order as $k=>$v){
			foreach($v as $kk=>$vv){
				$equation = time() - $vv['sendtime'];
				$some_days = $equation/(3600*24);
				if($some_days >= 10){
					$arr['status']  = 3;
					$order_list = M('shop_orders_list')->where(array('tb_id'=>$vv['tb_order_id'],'status'=>2,'orders_oid'=>$vv['oid']))->save($arr);
				}
			}
		}
		$this->oeders($orders);
	}
	/**
	 * 比对订单中的商品是否全部过期，全部过期将订单的状态更新
	 */
	public function oedes($orders=null){
		if(is_null($orders)) return FALSE;
		foreach($orders as $k => $v){
			$order_list_num1 = M('shop_orders_list')->where(array('orders_oid'=>$v))->count();
			$order_list_num2 = M('shop_orders_list')->where(array('orders_oid'=>$v,'status'=>3))->count();
			if($order_list_num1 == $order_list_num2){
				$order = M('shop_orders')->where(array('oid'=>$v))->save(array('status'=>3,'tb_status'=>3));
			}
		}
	}
}