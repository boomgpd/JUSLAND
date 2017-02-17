<?php

function alert($msg,$path=NULL,$parent=NULL){
    if($parent === true){
        $str=<<<str
        <script>alert('{$msg}');parent.location.href='{$path}'</script>
str;
    }else if(empty($path)){
		$str=<<<str
		<script>alert('{$msg}');history.back(-1)</script>
str;
	}else{
		$str=<<<str
		<script>alert('{$msg}');location.href='{$path}'</script>
str;
	};
	echo $str;
}

/** 
 * 创建在新页面打开方法
 */
function open($msg,$path=NULL,$parent=NULL){
        $str=<<<str
        <script>alert('{$msg}');window.open('{$path}')</script>
str;
	echo $str;
}

/**
 * curl的post请求方式
 */
function curl_post($url,$data){
	$ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_URL,$url);
    //为了支持cookie
    curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    //返回结果
    //拒绝验证ca证书
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
	curl_close ($ch);
    return $result;
}

/**
 * 导出excel表方法
 * $expTitle->标题，$expCellName->表头，$expTableData->导出数据
 */
function exportExcel($expTitle,$expCellName,$expTableData){
    $xlsTitle = iconv('utf-8', 'gb2312', $expTitle);//文件名称
    $fileName = $_SESSION['loginAccount'].date('_YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
    $cellNum = count($expCellName);
    $dataNum = count($expTableData);
    Vendor("Classes.PHPExcel");
    $objPHPExcel = new \PHPExcel();
    $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');
//  $objPHPExcel->getColumnDimension('A')->setWidth(50);
//	$objPHPExcel->getColumnDimension('B')->setWidth(240);
//	$objPHPExcel->getColumnDimension('C')->setWidth(80);
//	$objPHPExcel->getColumnDimension('D')->setWidth(90);
//	$objPHPExcel->getColumnDimension('E')->setWidth(150);
//	$objPHPExcel->getColumnDimension('F')->setWidth(120);
//	$objPHPExcel->getColumnDimension('G')->setWidth(140);
	
    $objPHPExcel->getActiveSheet(0)->mergeCells('A1:'.$cellName[$cellNum-1].'1');//合并单元格
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle);  
    for($i=0;$i<$cellNum;$i++){
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'2', $expCellName[$i][1]); 
    } 
      // Miscellaneous glyphs, UTF-8   
    for($i=0;$i<$dataNum;$i++){
      for($j=0;$j<$cellNum;$j++){
        $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+3), $expTableData[$i][$expCellName[$j][0]]);
      }             
    }  
   
	
    header('pragma:public');
    header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
    header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
    $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');  
    $objWriter->save('php://output'); 
    exit;   
}

/**
 * 发送邮件
 */
function think_send_mail($to, $name, $subject = '', $body = '', $attachment = null){

	$config = C('THINK_EMAIL');
	
	vendor('PHPMailer.class#phpmailer'); //从PHPMailer目录导class.phpmailer.php类文件
	vendor('SMTP');
	$mail = new PHPMailer(); //PHPMailer对象
	
	$mail->CharSet = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
	
	$mail->IsSMTP(); // 设定使用SMTP服务
	
	$mail->SMTPDebug = 0; // 关闭SMTP调试功能
	
	// 1 = errors and messages
	
	// 2 = messages only
	
	$mail->SMTPAuth = true; // 启用 SMTP 验证功能
	
	$mail->SMTPSecure = 'ssl'; // 使用安全协议
	
	$mail->Host = $config['SMTP_HOST']; // SMTP 服务器
	
	$mail->Port = $config['SMTP_PORT']; // SMTP服务器的端口号
	
	$mail->Username = $config['SMTP_USER']; // SMTP服务器用户名
	
	$mail->Password = $config['SMTP_PASS']; // SMTP服务器密码
	
	$mail->SetFrom($config['FROM_EMAIL'], $config['FROM_NAME']);
	
	$replyEmail = $config['REPLY_EMAIL']?$config['REPLY_EMAIL']:$config['FROM_EMAIL'];
	
	$replyName = $config['REPLY_NAME']?$config['REPLY_NAME']:$config['FROM_NAME'];
	
	$mail->AddReplyTo($replyEmail, $replyName);
	
	$mail->Subject = $subject;
	
	$mail->AltBody = "为了查看该邮件，请切换到支持 HTML 的邮件客户端"; 
	
	$mail->MsgHTML($body);
	
	$mail->AddAddress($to, $name);
	
	if(is_array($attachment)){ // 添加附件
	
	foreach ($attachment as $file){
	
	is_file($file) && $mail->AddAttachment($file);
	
	}
	
	}
	
	return $mail->Send() ? true : $mail->ErrorInfo;

}

/**
 * 生成二维码方法
 */
function qrcode($url,$size=4){
    Vendor('phpqrcode.phpqrcode');
    //生成二维码图片
    $object = new \QRcode();
    $level=3;
    $errorCorrectionLevel =intval($level) ;//容错级别
    $matrixPointSize = intval($size);//生成图片大小
    ob_clean();
    $img = $object->png($url, FALSE, $errorCorrectionLevel, $matrixPointSize, 3);
}
 
 
//在线交易订单支付处理函数
//函数功能：根据支付接口传回的数据判断该订单是否已经支付成功；
//返回值：如果订单已经成功支付，返回true，否则返回false；
function checkorderstatus($ordid){
    $Ord=M('orderlist');
    $ordstatus=$Ord->where('ordid='.$ordid)->getField('ordstatus');
    if($ordstatus==1){
        return true;
    }else{
        return false;    
    }
} 
 
//处理订单函数
//更新订单状态，写入订单支付后返回的数据
function orderhandle($parameter){
    $ordid=$parameter['out_trade_no'];
    $data['payment_trade_no']      =$parameter['trade_no'];
    $data['payment_trade_status']  =$parameter['trade_status'];
    $data['payment_notify_id']     =$parameter['notify_id'];
    $data['payment_notify_time']   =$parameter['notify_time'];
    $data['payment_buyer_email']   =$parameter['buyer_email'];
    $data['ordstatus']             =1;
    $Ord=M('orderlist');
    $Ord->where('ordid='.$ordid)->save($data);
}  
 
 
 /* 
*function：检测字符串是否由纯英文，纯中文，中英文混合组成 
*param string 
*return 1:纯英文;2:纯中文;3:中英文混合 
*/ 
function check_str($str=''){ 
	if(trim($str)==''){ 
		return ''; 
	} 
	$m=mb_strlen($str,'utf-8'); 
	$s=strlen($str); 
	if($s==$m){ 
		return 1; 
	} 
	if($s%$m==0&&$s%3==0){ 
		return 2; 
	} 
	return 3; 
}
 
/* 创建线上客服咨询函数
 * 用户主键$merchant_id
 * 咨询客服类型$custom_type
 * 后台客服主键$custom_id
 * 咨询内容$content
 * 是用户提问还是客服回答$type,1表示用户提问，2表示客户回答
 */
function sendMsg($merchant_id=null,$custom_id=null,$me_to_uid=null,$content=null,$type=null){
	// 指明给谁推送，为空表示向所有在线用户推送
	if($type){
		$to_uid = to_uid($type,$merchant_id,$custom_id);
	}else{
		$to_uid = $me_to_uid;
	}
	// 推送的url地址，上线时改成自己的服务器地址
	$push_api_url = "http://123.59.232.186:2358/";
	$post_data = array(
	   'type' => 'publish',
	   'content' => $content,
	   'to' => $to_uid,  
	);
	return curl_post($push_api_url, $post_data);
}

/**
 * 拼接推送给谁
 */
function to_uid($type=null,$merchant_id=null,$custom_id=null){
	if($type == 2){
		$to_uid = C('SYS_NAME').'_merchant_'.$merchant_id;
	}elseif($type == 1){
		$to_uid = C('SYS_NAME').'_custom_'.$custom_id;
	}
	return $to_uid;
}
 
/**
 *  post提交数据 
 * @param  string $url 请求Url
 * @param  array $datas 提交的数据 
 * @return url响应返回的html
 */
function sendPost($url, $datas) {
    $temps = array();	
    foreach ($datas as $key => $value) {
        $temps[] = sprintf('%s=%s', $key, $value);		
    }	
    $post_data = implode('&', $temps);
    $url_info = parse_url($url);
	if(empty($url_info['port']))
	{
		$url_info['port']=80;	
	}
    $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
    $httpheader.= "Host:" . $url_info['host'] . "\r\n";
    $httpheader.= "Content-Type:application/x-www-form-urlencoded\r\n";
    $httpheader.= "Content-Length:" . strlen($post_data) . "\r\n";
    $httpheader.= "Connection:close\r\n\r\n";
    $httpheader.= $post_data;
    $fd = fsockopen($url_info['host'], $url_info['port']);
    fwrite($fd, $httpheader);
    $gets = "";
	$headerFlag = true;
	while (!feof($fd)) {
		if (($header = @fgets($fd)) && ($header == "\r\n" || $header == "\n")) {
			break;
		}
	}
    while (!feof($fd)) {
		$gets.= fread($fd, 128);
    }
    fclose($fd);  
    
    return $gets;
}


/**
 * Json方式 查询订单物流轨迹
 */
function getOrderTracesByJson($ShipperCode,$number){
	$requestData= "{'OrderCode':'','ShipperCode':'$ShipperCode','LogisticCode':'$number'}";
	$config = C('EMS');
	$datas = array(
        'EBusinessID' => $config['EBusinessID'],
        'RequestType' => '1002',
        'RequestData' => urlencode($requestData) ,
        'DataType' => '2',
    );
    $datas['DataSign'] = encrypt($requestData, $config['AppKey']);
	$result=sendPost($config['ReqURL'], $datas);	
	
	//根据公司业务处理返回的信息......
	
	return $result;
}

/**
 * 电商Sign签名生成
 * @param data 内容   
 * @param appkey Appkey
 * @return DataSign签名
 */
function encrypt($data, $appkey) {
    return urlencode(base64_encode(md5($data.$appkey)));
}