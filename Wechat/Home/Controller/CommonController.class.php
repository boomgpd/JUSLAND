<?php
namespace Home\Controller;
use Think\Controller;
class CommonController extends Controller {
	protected $appid; 
	protected $token; 
	protected $appSecret; 
	
   public function __construct(){
		parent::__construct();
   		$this->appid = C('AppID');
		$this->token = C('Token');
		$this->appSecret = C('AppSecret');
		if(!$_SESSION['member_id']){
			$this->getOpenid();
		}
   }
   
   /**
    * 创建获取用户openid方法
    */
   public function getOpenid(){
   	if(!$_GET['code']){
   		$redirect_uri = urlencode('http://china.stinary.com/');
		$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$this->appid.'&redirect_uri='.$redirect_uri.'&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';
		redirect($url);
   	}else{
   		/*获取code*/
   		$code = $_GET['code'];
   		$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this->appid.'&secret='.$this->appSecret.'&code='.$code.'&grant_type=authorization_code';
   		/*获取access_token和openid*/
   		$data = file_get_contents($url);
		$data = json_decode($data,TRUE);
		/*获取用户信息*/
		$url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$data['access_token'].'&openid='.$data['openid'].'&lang=zh_CN';
		$data = file_get_contents($url);
		$data = json_decode($data,TRUE);
		$member_data = $this->add_member($data);
		$_SESSION['member_id'] = $member_data['id'];
		$_SESSION['member_name'] = $member_data['name'];
		$_SESSION['headimg'] = $member_data['headimg'];
		redirect(U('home/index/index'));
	}
   }
   
   /*创建添加用户方法*/
   public function add_member($data){
   		$oldData = M('member')->where(array('openid'=>$data['openid']))->find();
		if($oldData){
			$id = $oldData['member_id'];
			$name = $oldData['member_name'];
			$headimg = $oldData['headimg'];
		}else{
			$arr = array(
				'member_name'	=>	$data['nickname'],
				'headimg'	=>	$data['headimgurl'],
				'sex'			=>	$data['sex'],
				'openid'		=>	$data['openid']
			);
			$id = M('member')->add($arr);
			$name = $arr['member_name'];
			$headimg = $data['headimgurl'];
		}
   		
		return array('id'=>$id,'name'=>$name,'headimg'=>$headimg);
	}
}
