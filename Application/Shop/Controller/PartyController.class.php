<?php
namespace Shop\Controller;
use Common\Model\Shop_addressModel;

//商城前台订单控制器
class PartyController extends CommonController{
	private $address;
	
	public function __construct(){
        parent::__construct();
		$this->address = new Shop_addressModel;
		$this->left();
	}
	/**
	*获取用户信息的方法
	*/
	public function basic(){
		if(IS_POST){
			$data = I('post.');
			$re = M('member')->where(array('member_id'=>$_SESSION['member_id']))->save($data);
			if($re){
				alert('保存成功',U('basic'));
			}
		}
		$data = M('member')->where(array('member_id'=>$_SESSION['member_id']))->find();
		$data['shen'] = M('area')->field('area_id,aname')->where(array('area_id'=>$data['shen']))->find();
		$data['city'] = M('area')->field('area_id,aname')->where(array('area_id'=>$data['city']))->find();
		$area = M('area')->where(array('pid'=>0))->select();
		$this->assign('area',$area);
		$this->assign('data',$data);
		$this->display();
	}
	/**
	*获取城市的ajax
	*/
	public function areas(){
		if(!IS_AJAX) return FALSE; 
		$post = I('post.');
		$re = M('area')->where(array('pid'=>$post['area_id']))->select();
		$this->ajaxReturn($re);exit;
	}
	/**
	*手机号更换
	*/
	public function anquan(){
		$re = M('member')->where(array('member_id'=>$_SESSION['member_id']))->find();
		$this->assign('re',$re);
		$this->display();
	}
	/**
	*验证
	*/
	public function check_phone(){
		$get = I('get.');
		if($get['code'] == $_SESSION['code'] && $_SESSION['code_time']+300-time() >0 ){
			$re = M('member')->where(array('member_id'=>$_SESSION['member_id']))->save(array('phone'=>$get['phone']));
			if($re){
				alert('绑定成功',U('basic'));
			}else{
				alert('数据异常',U('anquan'));
			}
		}else{
			alert('验证码不正确',U('anquan'));
		}
	}
	/**
	*获取验证码
	*/
	public function code(){
		if(IS_AJAX && IS_POST){
			$data = I('post.phone');
			$_SESSION['conent'] = $data;
			if($_SESSION['code']){
				$time = $_SESSION['code_time']+300-time();
				if($time<0){
					unset($_SESSION['code_time']);
					unset($_SESSION['code']);
				}else{
					$this->ajaxReturn(array('type'=>'101','conent'=>'验证码已发送，请耐心等待','time'=>$time));exit;
				}
			}
			$code = rand(100000, 999999);
			$_SESSION['code'] = $code;
			$_SESSION['code_time'] = time();
			vendor('alidayu.TopSdk','','.php');
			$c = new \TopClient;
			$c->appkey = C('APP_KEY');
			$c->secretKey = C('APP_SECRET');
			$req = new \AlibabaAliqinFcSmsNumSendRequest;
			$req ->setExtend( "21" );
			$req ->setSmsType( "normal" );
			$req ->setSmsFreeSignName( "嘉士澜德" );
			$req ->setSmsParam("{code:'".$code."'}" );
			$req ->setRecNum($data);
			$req ->setSmsTemplateCode( "SMS_24785228" );
			$resp = $c ->execute( $req );
			$this->ajaxReturn(array('type'=>'100','conent'=>'验证码已发送'));die;
			$this->ajaxReturn(array('type'=>'success'));die;
		};
	}
	/**
	* 邮箱更换
	*/
	public function e_mail(){
		unset($_SESSION['email_code']);
		unset($_SESSION['email_conent']);
		unset($_SESSION['email_code_time']);
		$re = M('member')->where(array('member_id'=>$_SESSION['member_id']))->find();
		$this->assign('re',$re);
		$this->display();
	}
	/**
	* 邮箱发送验证码
	*/
	public function email_code(){
		if(!IS_AJAX) return FALSE;
		$data = I('post.email');
		// dump($data);die;
		if($_SESSION['email_code']){
		//判定邮箱验证码是否存在
			if(($_SESSION['email_code_time']+300)>time()){//判定邮箱验证码是否失效
				$this->ajaxReturn(array('type'=>'101','content'=>'验证码已发送，请耐心等待'));exit;
			}else{
				unset($_SESSION['code']);
				unset($_SESSION['code_time']);
			}
		}
		$_SESSION['email_code'] = rand(100000, 999999);
		$_SESSION['email_conent'] = $data;
		$_SESSION['email_code_time'] = time();
      	$to = $data;
    	$name = '嘉士澜德';
		$title = '嘉士澜德-婚里人';
		$body = '您好欢迎加入婚里人商家平台，您的修改密码的验证码为'.$_SESSION['email_code'].'<br/>';
		$body .= '五分钟内验证有效';
    	$re = think_send_mail($to,$name,$title,$body);
		if($re){
			$this->ajaxReturn(array('type'=>'100','conent'=>'验证码已发送成功'));
		}
	}
	/**
	* 验证邮箱验证码
	*/
	public function check_email(){
		$get = I('get.');
		if($get['email_code'] == $_SESSION['email_code'] && $_SESSION['email_code_time']+300-time() >0 ){
			$re = M('member')->where(array('member_id'=>$_SESSION['member_id']))->save(array('email'=>$get['email']));
			if($re){
				alert('绑定成功',U('basic'));
			}else{
				alert('数据异常',U('anquan'));
			}
		}else{
			alert('验证码不正确',U('anquan'));
		}
	}
	/**
	* 修改密码
	*/
	public function apwd(){
		$this->display();
	}
	/**
	* 验证密码
	*/
	public function check_pwd(){
		if(!IS_POST) return FALSE;
		$data = I('post.');
		$re = M('member')->where(array('member_id'=>$_SESSION['member_id']))->find();
		if($data['password_xin'] && $data['password'] && $data['password_yuan']){
			if(md5(md5($re['member_name']).md5($data['password_yuan'])) == $re['password']){
				if($data['password_xin'] == $data['password']){
					$pwd = md5(md5($re['member_name']).md5($data['password']));
					$bool = M('member')->where(array('member_id'=>$_SESSION['member_id']))->save(array('password'=>$pwd));
					if($bool){
						session_unset();
        				session_destroy();
						alert('密码修改完成，请重新登录',U('home/index/index'));
					}else{
						alert('数据异常');
					}
				}else{
					alert('俩次密码不正确');
				}
			}else{
				alert('原密码不正确');
			}
		}else{
			alert('请重新填写密码');
		}
	}
	public function left(){
		$left_data = M('member')->where(array('member_id'=>$_SESSION['member_id']))->find();
		$this->assign('left_data',$left_data);
	}
}