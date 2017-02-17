<?php
namespace Shop\Controller;
use Think\Controller;
header("Content-type: text/html; charset=utf-8"); 
/**
 * 创建商家展示系统控制器
 */
class WxpayController extends Controller {
	protected $merchant;
	
	public function __construct(){
		 //引入WxPayPubHelper
		parent::__construct();
        vendor('WxPayPubHelper.WxPayPubHelper','','.class.php');
	}
	
	/**
	 * 创建微信支付方法
	 */
    public function createQrcode($id=NULL){
        if(is_null($id)) return FALSE;//非法请求
        $order = M('shop_orders')->where(array('oid'=>$id,'member_id'=>$_SESSION['member_id'],'status'=>0,'is_del'=>0))->find();
        if(is_null($order)) return FALSE;//非法请求或二次请求
        $gids = M('shop_orders_list')->where(array('orders_oid'=>$id,'is_del'=>0))->getField('goods_gid',true);//获得所有商品id
        $name = '';//拼接商品名称
        $gids = array_unique($gids);
        foreach($gids as $k => $v){
            $name .= ','.M('shop_goods')->where(array('gid'=>$v))->getField('gname');
        };
        $name = ltrim($name,',');
        //使用统一支付接口
        $unifiedOrder = new \UnifiedOrder_pub();
        
        //设置统一支付接口参数
        //设置必填参数
        //appid已填,商户无需重复填写
        //mch_id已填,商户无需重复填写
        //noncestr已填,商户无需重复填写
        //spbill_create_ip已填,商户无需重复填写
        //sign已填,商户无需重复填写
        $unifiedOrder->setParameter("body",$name);//商品描述
        //自定义订单号，此处仅作举例
        // $timeStamp = time();
        $out_trade_no = C('WxPayConf_pub.APPID').$order['number'];
        $unifiedOrder->setParameter("out_trade_no","$out_trade_no");//商户订单号 
        $unifiedOrder->setParameter("total_fee",$order['total']*100);//总金额
        $unifiedOrder->setParameter("notify_url", C('WxPayConf_pub.NOTIFY_URL'));//通知地址 
        $unifiedOrder->setParameter("trade_type","NATIVE");//交易类型
        //获取统一支付接口结果
        $unifiedOrderResult = $unifiedOrder->getResult();
        //商户根据实际情况设置相应的处理流程
        if ($unifiedOrderResult["return_code"] == "FAIL") 
        {
            //商户自行增加处理流程
            // echo "通信出错：".$unifiedOrderResult['return_msg']."<br>";
        }
        elseif($unifiedOrderResult["result_code"] == "FAIL")
        {
            //商户自行增加处理流程
            // echo "错误代码：".$unifiedOrderResult['err_code']."<br>";
            // echo "错误代码描述：".$unifiedOrderResult['err_code_des']."<br>";
        }
        elseif($unifiedOrderResult["code_url"] != NULL)
        {
            //从统一支付接口获取到code_url
            $code_url = $unifiedOrderResult["code_url"];
            //商户自行增加处理流程
            //......
        }
        // $this->assign('out_trade_no',$out_trade_no);
        // $this->assign('code_url',$code_url);
        // $this->assign('unifiedOrderResult',$unifiedOrderResult);
        return $code_url;
        // $this->display('qrcode');
    }
     
	/**
	 * 验证是否支付成功
	 */  
	public function notify(){
        //使用通用通知接口
        $notify = new \Notify_pub();
         
        //存储微信的回调
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        $notify->saveData($xml);

        //验证签名，并回应微信。
        //对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
        //微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
        //尽可能提高通知的成功率，但微信不保证通知最终能成功。
        
        if($notify->checkSign() == FALSE){
            M('wxpay')->add(array('type'=>3));
            $notify->setReturnParameter("return_code","FAIL");//返回状态码
            $notify->setReturnParameter("return_msg","签名失败");//返回信息
        }else{
            $notify->setReturnParameter("return_code","SUCCESS");//设置返回码
        }
        $returnXml = $notify->returnXml();
        echo $returnXml;
        //==商户根据实际情况设置相应的处理流程，此处仅作举例=======
         
        //以log文件形式记录回调信息
        //         $log_ = new Log_();
        $log_name= __ROOT__."/Public/notify_url.log";//log文件路径
         
       // log_result($log_name,"【接收到的notify通知】:\n".$xml."\n");
         
        if($notify->checkSign() == TRUE){
            $number = substr($notify->data['out_trade_no'],-10);
            if ($notify->data["return_code"] == "FAIL") {
                //进行订单处理
                M('shop_orders')->where(array('number'=>$number))->save(array('is_del'=>1));
                //此处应该更新一下订单状态，商户自行增删操作
//              log_result($log_name,"【通信出错】:\n".$xml."\n");
            }
            elseif($notify->data["result_code"] == "FAIL"){
                M('shop_orders')->where(array('number'=>$number))->save(array('is_del'=>1));
                //此处应该更新一下订单状态，商户自行增删操作
//              log_result($log_name,"【业务出错】:\n".$xml."\n");
            }
            else{
                //进行订单处理
                $model = new \Common\Model\Shop_orderModel;
                M('shop_orders')->where(array('number'=>$number))->setField(array('status'=>1,'paytime'=>time()));
                $model->deal($number);
                //此处应该更新一下订单状态，商户自行增删操作
//              log_result($log_name,"【支付成功】:\n".$xml."\n");
            }
             
            //商户自行增加处理流程,
            //例如：更新订单状态
            //例如：数据库操作
            //例如：推送支付完成信息
        }
    }
	 
	
}