<?php
namespace Common\Model;
use Think\Model;

//订单模型
class Shop_orderModel extends Model{
	
	protected $tableName = 'shop_orders';

	
	protected $_validate = array(
		array('address','require','请选择收货地址',1),
	);

	//添加订单
	public function store($post){
		$cart = new Shop_cartModel;
		$address = new Shop_addressModel;
		$cart_data = $cart->where(array('id'=>implode(',',$post['ids']),'member_id'=>$_SESSION['member_id'],'is_del'=>0))->select();//获得购物车数据
		if(is_null($cart_data)){
			$this->error = '购物车商品不存在或已失效';return FALSE;
		};
		$cart_data = $cart->get_data($cart_data);//获得商品数据
		if(empty($cart_data['goods'])){
			$this->error = '购物车数据异常';return FALSE;
		};
		$ress_data = $address->where(array('id'=>$post['address'],'member_id'=>$_SESSION['member_id'],'is_del'=>0))->find();//获得收货地址数据
		if(is_null($ress_data)){
			$this->error = '收货地址错误或已失效';return FALSE;
		};
		//中文收货地址
		$ress_data['province'] = M('Area')->where(array('area_id'=>$ress_data['province']))->getField('aname');
		$ress_data['city'] = M('Area')->where(array('area_id'=>$ress_data['city']))->getField('aname');
		//拼接需要的数据
		$o_data = array();
		$o_data['consignee'] = $ress_data['linkman'];
		$o_data['address'] = $ress_data['province'].$ress_data['city'].$ress_data['details'];
		$o_data['phone'] = $ress_data['phone'];
		$o_data['total'] = $cart_data['total'];
		$o_data['number'] = $this->get_number();
		$o_data['addtime'] = time();
		$o_data['member_id'] = $_SESSION['member_id'];
		if($ress_data['coding']){
			$o_data['coding'] = $ress_data['coding'];
		}
		$oid = $this->add($o_data);
		//添加订单列表数据
		foreach($cart_data['goods'] as $k => $v){
			$ol_data = array();
			$ol_data['num'] = $v['num'];
			$ol_data['subtotal'] = $v['total'];
			$ol_data['goods_list_glid'] = $v['glid'];
			$ol_data['goods_gid'] = $v['gid'];
			$ol_data['orders_oid'] = $oid;
			M('shop_orders_list')->add($ol_data);
		};
		$cart->where(array('merber_id'=>$_SESSION['member_id']))->save(array('is_del'=>1));
		return $oid;
	}
	/**
	 * 立即购买添加订单
	 */
	public function store_get($post){
		$cart = new Shop_cartModel;
		$address = new Shop_addressModel;
		$cart_data = $cart->noce_data($post);//获得商品数据
		if(empty($cart_data['goods'])){
			$this->error = '购物车数据异常';return FALSE;
		};
		$ress_data = $address->where(array('id'=>$post['address'],'member_id'=>$_SESSION['member_id'],'is_del'=>0))->find();//获得收货地址数据
		if(is_null($ress_data)){
			$this->error = '收货地址错误或已失效';return FALSE;
		};
		//中文收货地址
		$ress_data['province'] = M('Area')->where(array('area_id'=>$ress_data['province']))->getField('aname');
		$ress_data['city'] = M('Area')->where(array('area_id'=>$ress_data['city']))->getField('aname');
		//拼接需要的数据
		$o_data = array();
		$o_data['consignee'] = $ress_data['linkman'];
		$o_data['address'] = $ress_data['province'].$ress_data['city'].$ress_data['details'];
		$o_data['phone'] = $ress_data['phone'];
		$o_data['total'] = $cart_data['total'];
		$o_data['number'] = $this->get_number();
		$o_data['addtime'] = time();
		$o_data['member_id'] = $_SESSION['member_id'];
		if($ress_data['coding']){
			$o_data['coding'] = $ress_data['coding'];
		}
		$oid = $this->add($o_data);
		//添加订单列表数据
		foreach($cart_data['goods'] as $k => $v){
			$ol_data = array();
			$ol_data['num'] = $v['num'];
			$ol_data['subtotal'] = $v['total'];
			$ol_data['goods_list_glid'] = $v['glid'];
			$ol_data['goods_gid'] = $v['gid'];
			$ol_data['orders_oid'] = $oid;
			M('shop_orders_list')->add($ol_data);
		};
		$cart->where(array('member_id'=>$_SESSION['member_id']))->save(array('is_del'=>1));
		return $oid;
	}

	//获得订单号
	private function get_number(){
		$bt = 'QWERTYUIOPASDFGHJKLZXCVBNM1234567890';
		$length = strlen($bt);
		$zz = '';
		for($i = 0;$i < 10;$i++){
			$rand = rand(0,$length-1);
			$zz .= substr($bt,$rand,1);
		};
		$re = $this->where(array('number'=>$zz,'is_del'=>0))->find();
		if(!is_null($re)){
			$zz = $this->get_number();
		};
		return $zz;
	}

	//处理订单
	public function deal($number=NULL){
		if(is_null($number)) return FALSE;
		$order = $this->where(array('number'=>$number,'is_del'=>0))->find();
		if(is_null($order)) return FALSE;
		M('shop_orders_list')->where(array('orders_oid'=>$order['oid'],'status'=>0))->save(array('status'=>1));//获得所有对应此订单的更改状态
	}

}
?>