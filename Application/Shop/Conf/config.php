<?php
return array (
	 '__SHOP_LIST_NUM__' => '12',
  	//支付宝配置参数
	'alipay_config'=>array(
			    'partner' =>'2088521345453884',   //这里是你在成功申请支付宝接口后获取到的PID；
			    'key'=>'oi6vcsanwulq6r2p8doyftx9zgnaccrw',//这里是你在成功申请支付宝接口后获取到的Key
			    'sign_type'=>strtoupper('MD5'),
			    'input_charset'=> strtolower('utf-8'),
			    'cacert'=> getcwd().'\\cacert.pem',
			    'transport'=> 'http',
	      		),
	     //以上配置项，是从接口包中alipay.config.php 文件中复制过来，进行配置；
	    
	'alipay'   =>array(
	 //这里是卖家的支付宝账号，也就是你申请接口时注册的支付宝账号
	'seller_email'=>'2278281929@qq.com',
	//这里是异步通知页面url，提交到项目的Pay控制器的notifyurl方法；
	'notify_url'=>'http://wp.c-wia.com/index.php/Shop/Alipay/notifyurl', 
	//这里是页面跳转通知url，提交到项目的Pay控制器的returnurl方法；
	'return_url'=>'http://wp.c-wia.com/index.php/Shop/Alipay/returnurl',
	//支付成功跳转到的页面，我这里跳转到项目的User控制器，myorder方法，并传参payed（已支付列表）
	'successpage'=>'User/myorder?ordtype=payed',   
	//支付失败跳转到的页面，我这里跳转到项目的User控制器，myorder方法，并传参unpay（未支付列表）
	'errorpage'=>'User/myorder?ordtype=unpay', 
	),
	//微信支付参数
	'WxPayConf_pub'=>array(
		'APPID'=>'wx949e6c2ead7cd9fc',
		'MCHID'=>'1416775302',
		'KEY'=>'wUrLSQD3uxQBDQi51Bw5sNYmJKDK8gRf',
		'APPSECRET'=>'8a018f5ddd23bc3d92781b98cfe0ff4f',
		'NOTIFY_URL'=>'http://wp.c-wia.com/index.php/Shop/Wxpay/notify',
	),
	/* 默认设定 */
    'DEFAULT_CONTROLLER'    =>  'Index', // 默认控制器名称
    'DEFAULT_ACTION'        =>  'index', // 默认操作名称
);  
?>