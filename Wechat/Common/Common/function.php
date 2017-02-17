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
    $result = json_decode(curl_exec($ch),TRUE);
	curl_close ($ch);
    return $result;
}

/**
 * 创建curl get请求
 */
function curl_get($url){
	echo 1;die;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	$output = curl_exec($ch); 
	curl_close($ch);
	return $output;
}
