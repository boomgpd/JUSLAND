<?php namespace Home\Controller;
use Think\Controller;
use Common\Model\Diy_marrierModel;

/**
 * 创建前台登录
 */
class LoginController extends  CommonController {
	
	protected $member;
	protected $merchant;
	protected $verify;//验证码类
	protected $diy_marrire;
	
	public function __construct(){
		parent::__construct();
		$this-> member=D('Member');
		$this->verify = new \Think\Verify();
		$this->merchant = D('Merchant');
		$this->diy_marrire = new Diy_marrierModel;
	}
	
	/**
	 * 创建前台登录方法
	 */
	 public function login(){
		 if($_SESSION['merchant_id']){
		 	redirect(U('merchant/index/index'));
		 }else if($_SESSION['member_id']){
		 	redirect(U('index/index'));
		 }else{
	 		$this->display();
		 }
	 }
	 
	 /**
	  * 创建退出登录方法
	  */
	  public function out(){
	  	unset($_SESSION['member_id']);
		unset($_SESSION['merchant_id']);
		redirect(U('home/index/index'));
	  }

	/**
	 * 创建检验账号密码是否正确
	 */
	 public function check_pass(){
	 	if(!IS_AJAX) return FALSE;
		/**
		 * 首先判定验证码是否正确
		 * 1.判定数据内有没有m_name字段；
		 * 2.有的话就是商家登录，调用商家模型内登录验证方法，返出对应信息
		 * 3.没有的就是用户登录，调用用户模型内登录验证方法，返出对应信息
		 */
		 $data = I('post.');
		 $re = $this->verify->check($data['code']);
		 if(!$re) $this->ajaxReturn(array('type'=>101,'content'=>'验证码输入错误'));
		 if($data['m_name']){
		 	$re = $this->merchant->check_login($data);
			$this->ajaxReturn($re);exit;
		 }else{
		 	$re = $this->member->check_login($data);
			$this->ajaxReturn($re);exit;
		 }
	 }	
	 
	 /**
	  * 判断登录信息是否正确
	  */
	 public function check_login(){
	 	if(!IS_AJAX) return false;
	 	$data = I('post.');
	 	$re = $this->verify->check($data['code']);
	 	if(!$re) $this->ajaxReturn(array('type'=>101,'content'=>'验证码输入错误'));
	 	
	 	//婚礼人登录
	 	if($data['form_type'] == 'diy_marrier'){
	 		unset($data['form_type']);
	 		unset($data['code']);
		 	$re = $this->diy_marrire->check_login($data);
			$this->ajaxReturn($re);exit;
		 }else if($data['form_type'] == 'member'){
		 //用户登录
		 	unset($data['form_type']);
	 		unset($data['code']);
		 	$re = $this->member->check_login($data);
			$this->ajaxReturn($re);exit;
		 }else if($data['form_type'] == 'merchant'){
		 //用户登录
		 	unset($data['form_type']);
	 		unset($data['code']);
		 	$re = $this->merchant->check_login($data);
			$this->ajaxReturn($re);exit;
		 }
	 	
	 	
	 }
	 
	 

}