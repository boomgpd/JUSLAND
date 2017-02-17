<?php 
namespace Admin\Controller;
use Think\Controller;
use Org\Util\Rbac;
class LoginController extends Controller {
	protected $admin;
	
	public function __construct(){
		parent::__construct();
		$this->admin = M('admin');
	}
	
	/**
	 * 登录页面
	 */
	public function login(){
		if(IS_POST){
			$data = I('post.');
			$re = $this->admin->where(array('admin_name'=>$data['admin_name']))->find();
			if(!$re) {
				echo '<script type="text/javascript">alert("用户名不存在");</script>';
			}else if($re['password'] != md5(md5($data['admin_name']).md5($data['password']))){
				echo '<script type="text/javascript">alert("密码输入错误");</script>';
			}else{
				//写入session值
				session(C('USER_AUTH_KEY'),$re['admin_id']);
				session('admin_name',$re['admin_name']);
				//超级管理员不验证
				if(C('RBAC_SUPERADMIN') == $re['admin_name']){
					session(C('ADMIN_AUTH_KEY'),true);
				};
		        //存储用户权限
		        RBAC::saveAccessList();
				redirect(U('index/index'));
			}
		};
		$this->display();
	}
	
	/**
	 * 退出方法
	 */
	public function outLogin(){
		unset($_SESSION[C('USER_AUTH_KEY')]);
		unset($_SESSION['admin_name']);
		session_destroy();
		redirect(U('index/index'));
	}
	
	/**
	 * 创建修改密码方法
	 */
	 public function changePass(){
	 	if(IS_POST){
	 		$pwd = I('post.new_pwd');
			$pwd = md5(md5($_SESSION['admin_name']).md5($pwd));
	 		$re = M('admin')->where(array('admin_id'=>$_SESSION['admin_id']))->save(array('password'=>$pwd));
			if(!$re){
				$this->error('更改密码失败');
			}else{
				unset($_SESSION['admin_id']);
				unset($_SESSION['admin_name']);
				$this->success('密码更改成功',U('login/login'));
			}
	 	}
		$this->display();
	 }
	 
	 /**
	  * 创建个人中心页面
	  */
	  public function selfCenter(){
	  	$data = M('admin')->where(array('admin_id'=>$_SESSION['admin_id']))->find();
		$data['addtime'] = date('Y-m-d H:i:s');
		$this->assign('data',$data);
		$this->display();
	  }
	  
	  /**
	   * 创建验证原密码是否正确方法
	   */
	   public function check_old_pwd(){
	   		if(!IS_AIAX) return FALSE;
			$old_pwd = I('post.old_pwd');
			$old_pwd =  md5(md5($_SESSION['admin_name']).md5($old_pwd));
			$right_pwd = M('admin')->where(array('admin_id'=>$_SESSION['admin_id']))->getField('password');
			if($right_pwd != $old_pwd){
				$this->ajaxReturn('false');exit;
			}else{
				$this->ajaxReturn('true');exit;
			}
	   }
}