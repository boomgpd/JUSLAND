<?php namespace Merchant\Controller;
use Think\Controller;
use Common\Model\AreaModel;
/**
 * 商家管理系统控制器
 * boom
 * 08/12
 */
class IndexController extends CommonController {
	protected $area;
	
	public function __construct(){
		parent::__construct();
		$this->area = new AreaModel;
	}
	
	/**
	 * 创建商家管理首页方法
	 */
	 public function index(){
	 	$data = M('merchant')->field('m_name')->where(array('merchant_id'=>$_SESSION['merchant_id']))->find();
		$data['logo'] = M('merchant_message')->where(array('merchant_id'=>$_SESSION['merchant_id']))->getField('logo_yuan');
		$this->assign('data',$data);
	 	$this->display();
	 }

	 //切换状态
	 public function cut(){
	 	$model = new \Merchant\Model\Merchant_messageModel;
	 	$id = $_SESSION['merchant_id'];
	 	$status = M('Merchant_message')->where(array('merchant_id'=>$id))->getField('is_show');
	 	if(!$status){
	 		$result = $model->verify($id);
		 	if(!$result){
		 		alert($model->getError());die;
		 	};
		 };
	 	if($status){
	 		M('Merchant_message')->where(array('merchant_id'=>$id))->save(array('is_show'=>0));
	 		$msg = '下线成功';
	 	}else{
	 		M('Merchant_message')->where(array('merchant_id'=>$id))->save(array('is_show'=>1));
	 		$msg = '上线成功';
	 	};
	 	alert($msg,U('Index/index'),true);die;
	 }
	
	/**
	 * 创建商家商家欢迎页面
	 */
	 public function welcome(){
	 	$this->display();
	 }
	
	/**
	 * 创建退出登录方法
	 */
	 public function out(){
	 	unset($_SESSION['merchant_id']);
		redirect(U('home/Index/index'));
	 }
	 
	 /**
	  * 创建修改密码和用户名信息
	  */
	  public function change_pwd(){
	  	if(IS_POST){
	  		$post = I('post.');
			$userData = M('Merchant')->where(array('merchant_id'=>$_SESSION['merchant_id']))->find();
			if($userData['password'] != md5(md5($userData['m_name']).md5($post['old_pwd']))){
				$this->error('原密码不正确');
			}else if($post['new_pwd'] != $post['reput_pwd']){
				$this->error('两次密码不一致');
			}else if(mb_strlen($post['new_pwd']) != 6){
				$this->error('密码长度要求为6位');
			}else{
				$new_pwd = md5(md5($userData['m_name']).md5($post['new_pwd']));
				$userData['password'] = $new_pwd;
				M('Merchant')->save($userData);
				$this->success('修改成功',U('Index/index'));
			};
	  	};
		$this->display();
	  }
	  
	 //判断原密码是否正确
	 public function check_old_pwd(){
	 	if(!IS_AJAX) return FALSE;
		$userData = M('Merchant')->where(array('merchant_id'=>$_SESSION['merchant_id']))->find();
		if($userData['password'] != md5(md5($userData['m_name']).md5(I('post.data')))){
			echo false;
		}else{
			echo true;
		};die;
	 }

}