<?php
namespace Shop\Controller;
use Common\Model\Shop_orderModel;
//商城前台结算控制器
class PayController extends CommonController {

	private $order;

	public function __construct(){
		parent::__construct();
		$this->order = new Shop_orderModel;
	}

	//购物车结算页
	public function cart($id=NULL){
		if(is_null($id)) return FALSE;
		$data = $this->order->where(array('oid'=>$id,'member_id'=>$_SESSION['member_id']))->find();//获得订单数据
		if($data['tb_status'] == 0){
			$msg = '请先联系客服确认快递价格再付款，以免出现二次付款情况';
			$path = 'http://wpa.qq.com/msgrd?v=3&uin=2032142893&site=qq&menu=yes';
			open($msg,$path);
			echo "<script>location.href=".U('selfcenter/index')."</script>";
		}else{
			$model = new WxpayController;
			$code = $model->createQrcode($id);//获得微信二维码
			$this->assign('code',$code);
		}
		if(is_null($data)) return FALSE;
		$this->assign('data',$data);
		$this->display();
	}

	//支付宝支付
	public function pay($number=NULL){
		if(is_null($number)) return FALSE;
		$data = $this->order->where(array('number'=>$number,'member_id'=>$_SESSION['member_id']))->find();//获得订单数据
		if(is_null($data)) return FALSE;
		$model = new AlipayController;
		$model -> doalipay($data['oid']);
	}

	//异步检测支付
	public function refer(){
		if(!IS_AJAX || !IS_POST) return FALSE;
		$number = I('post.number');
		$data = $this->order->where(array('number'=>$number,'member_id'=>$_SESSION['member_id']))->find();
		if(is_null($data)){
			$this->ajaxReturn(array('type'=>100));die;
		}else if($data['status'] == 0){
			$this->ajaxReturn(array('type'=>101));die;
		}else if($data['status'] == 1){
			$this->ajaxReturn(array('type'=>103));die;
		};
	}
	/**
	 * 余额宝支付
	 */
	public function balance(){
		if(!IS_AJAX) return FALSE;
		$post = I('post.');
		$merchant = M('merchant')->where(array('member_id'=>$_SESSION['member_id']))->find();
		if(!$merchant){
			$this->ajaxReturn(array('type'=>404,'content'=>'请先登录'));exit;
		} 
		$order = M('shop_orders')->where(array('oid'=>$post['oid']))->find();
		if(!$order){
			$this->ajaxReturn(array('type'=>404,'content'=>'页面出错'));exit;
		} 
		if($merchant['pay_pwd'] == md5($post['pwd'])){
			if($merchant['balance'] >= $order['total']){
				M()->startTrans();
				$pay_order = M('shop_orders')->where(array('oid'=>$post['oid']))->save(array('status'=>1));
				if(!$pay_order){
					$error = array('type'=>404,'content'=>'支付失败，数据异常，请稍后再试');
					M()->rollback();
				}
				$pay_order_list = M('shop_orders_list')->where(array('orders_oid'=>$post['oid']))->save(array('status'=>1));
				if(!$pay_order_list){
					$error = array('type'=>404,'content'=>'支付失败，数据异常，请稍后再试');
					M()->rollback();
				}
				$pay_merchant = M('merchant')->where(array('member_id'=>$_SESSION['member_id']))->save(array('balance'=>($merchant['balance']-$order['total'])));
				if(!$pay_merchant){
					$error = array('type'=>404,'content'=>'支付失败，数据异常，请稍后再试');
					M()->rollback();
				}
				if($pay_merchant && $pay_order && $pay_order_list){
					M()->commit();
					$this->ajaxReturn(array('type'=>100,'content'=>'支付成功'));exit;
				}else{
					$this->ajaxReturn($error);exit;
				}
			}else{
				$this->ajaxReturn(array('type'=>404,'content'=>'余额不足，可以换别的方式支付'));exit;
			}		
		}else{
			$this->ajaxReturn(array('type'=>404,'content'=>'支付失败，支付密码错误'));exit;
		}
	}
}