<?php
namespace Admin\Controller;
use Think\Controller;
class WechatController extends Controller {
	public static $token;
	public static $appId;
	public static $appSecret;
	
	
	public function __construct(){
		parent::__construct();
		self::$token = C('Token');
		self::$appId = C('AppID');
		self::$appSecret = C('AppSecret');
	}
	
	/**
	 * 创建显示微信菜单栏方法
	 */
	public function showMenu(){
		/**
		 * 获取一级三个菜单栏
		 */
		$data = M('wx_list')->field('w_id,name')->where(array('pid'=>0))->select();
		foreach($data as $k=>&$v){
			$v['sub_button'] = M('wx_list')->field('type,name,url,w_id')->where(array('pid'=>$v['w_id']))->select();
		}
		$this->assign('data',$data);
		$this->display();
	}
	
	/**
	 * 创建验证子类是否超过上限
	 */
	public function check_son_num(){
		$w_id = I('post.w_id');
		$num = M('wx_list')->where(array('pid'=>$w_id))->count();
		if($num >= 4){
			$this->ajaxReturn('false');exit;
		}else{
			$this->ajaxReturn($num+1);exit;
		}
	}
	
	/**
	 * 创建编辑菜单栏方法
	 */
	public function edit(){
		$data = I('post.');
		$re = M('wx_list')->save($data);
		$this->createButton();
		if($re || $re !== FALSE){
			$this->ajaxReturn('true');exit;
		}else{
			$this->ajaxReturn('false');exit;
		}
	}
	
	/**
	 * 创建添加菜单栏方法
	 */
	public function add_son(){
		$data = I('post.');
		$num = M('wx_list')->where(array('pid'=>$data['w_id']))->count();
		if($num >= 4){
			$this->ajaxReturn('false');exit;
		}
		$re = M('wx_list')->add($data);
		$this->createButton();
		if($re){
			$this->ajaxReturn('true');exit;
		}else{
			$this->ajaxReturn('false');exit;
		}
	}
	
	/**
	 * 创建删除菜单栏方法
	 */
	public function del(){
		$w_id = I('post.w_id');
		$re = M('wx_list')->where(array('w_id'=>$w_id))->delete();
		$re_two = $this->createButton();
		if($re){
			$this->ajaxReturn('true');
		}else{
			$this->ajaxReturn('false');
		}
	}
	
	
	/**
	 * 创建菜单栏方法
	 */
	public function createButton(){
		$data['button'] = M('wx_list')->field('w_id,name')->where(array('pid'=>0))->select();
		foreach($data['button'] as $k=>&$v){
			$v['sub_button'] = M('wx_list')->field('type,name,url')->where(array('pid'=>$v['w_id']))->select();
			unset($v['w_id']);
		}
		$token = self::getToken();
		$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=". $token;
		//初始化curl
		$curl = curl_init();
		//需要获取的URL地址，也可以在curl_init()函数中设置。
		curl_setopt($curl, CURLOPT_URL, $url);
		//数组转json,并且不编码
		$data = json_encode($data,JSON_UNESCAPED_UNICODE);
		$data = str_replace('\/', '/', $data);
		//发送一个post请求
		curl_setopt($curl, CURLOPT_POST, 1);
		//发送的post的数据
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		//将curl_exec()获取的信息以文件流的形式返回，而不是直接输出。
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($curl);
		//关闭
		curl_close($curl);
		return $output;
	}
	
	//服务器验证
	public static function validate(){
		if(isset($_GET["echostr"]) && isset($_GET["signature"]) && isset($_GET["timestamp"]) && isset($_GET["nonce"])){
			$echoStr = $_GET["echostr"];   
			$signature = $_GET["signature"];
			$timestamp = $_GET["timestamp"];
			$nonce = $_GET["nonce"];           
			$token = self::$token;
			$tmpArr = array($token, $timestamp, $nonce);
			// use SORT_STRING rule
			sort($tmpArr, SORT_STRING);
			$tmpStr = implode( $tmpArr );
			$tmpStr = sha1( $tmpStr );
			if( $tmpStr == $signature){
			    echo $echoStr;
				return true;
			}
		}
		return false;
	}
	
	//获得不同的实例对象
	public static function getInstance($name){
		$className = "WeiXin" . ucfirst($name);
		include_once "./WeiXinAPI/{$className}" . '.php';
		$obj = new $className;
		return $obj;
	}
	
	public static function getWxObj(){
		//1.接收微信服务器推送过来的消息(xml格式,字符串类型)
		$wxXML = $GLOBALS['HTTP_RAW_POST_DATA'];
//		$wxXML = <<<str
//<xml>
// <ToUserName><![CDATA[toUser]]></ToUserName>
// <FromUserName><![CDATA[fromUser]]></FromUserName> 
// <CreateTime>1348831860</CreateTime>
// <MsgType><![CDATA[text]]></MsgType>
// <Content><![CDATA[1]]></Content>
// <MsgId>1234567890123456</MsgId>
// </xml>
//str;
		//2.处理消息类型
		$wxObj = simplexml_load_string($wxXML);
		return $wxObj;
	}
	
	//回复文本消息
	public static function responseText($Content){
		$wxObj = self::getWxObj();
		//发送方
		$FromUserName = $wxObj->ToUserName;
		//接收方
		$ToUserName = $wxObj->FromUserName;
		$CreateTime = time();
		$MsgType = 'text';
		//给用户回复默认欢迎消息
		$template = <<<str
				<xml>
				<ToUserName><![CDATA[{$ToUserName}]]></ToUserName>
				<FromUserName><![CDATA[{$FromUserName}]]></FromUserName>
				<CreateTime>{$CreateTime}</CreateTime>
				<MsgType><![CDATA[{$MsgType}]]></MsgType>
				<Content><![CDATA[{$Content}]]></Content>
				</xml>
str;
		echo $template;exit;
	}
	
	public static function responseNews($arr){
		$wxObj = self::getWxObj();
		//发送方
		$FromUserName = $wxObj->ToUserName;
		//接收方
		$ToUserName = $wxObj->FromUserName;
		$CreateTime = time();
		$template = "<xml>
				<ToUserName><![CDATA[{$ToUserName}]]></ToUserName>
				<FromUserName><![CDATA[{$FromUserName}]]></FromUserName>
				<CreateTime>{$CreateTime}</CreateTime>
				<MsgType><![CDATA[news]]></MsgType>
				<ArticleCount>".count($arr)."</ArticleCount>
				<Articles>";
		foreach ($arr as $v) {
			$template .= "<item>
					<Title><![CDATA[{$v['title']}]]></Title> 
					<Description><![CDATA[{$v['description']}]]></Description>
					<PicUrl><![CDATA[{$v['picurl']}]]></PicUrl>
					<Url><![CDATA[{$v['url']}]]></Url>
					</item>";
		}
		$template .= "</Articles></xml>";
		echo $template;exit;
	}
	
	public static function getToken(){
		//请求地址
		$url = "https://api.weixin.qq.com/cgi-bin/token";
		//获取access_token填写client_credential 
		$grant_type = 'client_credential';
		//第三方用户唯一凭证 
		$appid = self::$appId;
		//第三方用户唯一凭证密钥，即appsecret 
		$secret = self::$appSecret;
		//最终地址
		$url .= "?grant_type={$grant_type}&appid={$appid}&secret={$secret}";
		//请求
		$json = file_get_contents($url);
		//把返回的json转为对象
		$arr = json_decode($json,true);
		return $arr['access_token'];
	}
   
}
