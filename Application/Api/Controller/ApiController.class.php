<?php
namespace Api\Controller;
use Think\Controller;

//接口控制器
class ApiController extends Controller{

	private $arr = array(
		array('type'=>'error','error'=>'非法请求'),
		array('type'=>'error','error'=>'未审核通过'),
		array('type'=>'error','error'=>'用户不存在'),
		array('type'=>'error','error'=>'密码错误'),
		array('id'=>'','name'=>'','type'=>'success'),
	);

	/*
	*跳转请求
	*post 请求
	*@param sys_message 需要跳转的页面信息
	*@param user_message 用户信息
	*/
	public function skip(){
		if(!IS_POST){
			echo json_encode($this->arr[0]);die;
		};
		$post = I('post.');
		$re = curl_post('http://china.stinary.com/index.php/Api/Api/Curl_post',$post['user_message']);
		if($re['type'] == 'error'){
			alert($re['error']);die;
		}else{
			$_SESSION['merchant_id'] = $re['id'];
			$_SESSION['merchant_name'] = $re['name'];
			$_SESSION['identity'] = $post['user_message']['identity'];
			$url = implode('/',$post['sys_message']);
			alert('欢迎',U($url));die;
		};
	}

	/*登录验证
	*post请求
	*@param username 用户名
	*@param password 密码
	*@param identity 登录者身份
	*/
	public function Curl_post(){
		if(!IS_POST){
			echo json_encode($this->arr[0]);die;
		};
		$post = I('post.');
		if(!isset($post['username']) || !isset($post['password']) || !isset($post['identity'])){
			echo json_encode($this->arr[0]);die;
		};
		switch ($post['identity']) {
			case 'Merchant':
				$userData = M('Merchant')->where(array('is_del'=>0,'m_name'=>$post['username']))->find();
				if(is_null($userData)){
					echo json_encode($this->arr[2]);die;
				}else if($userData['is_apply'] != 1){
					echo json_encode($this->arr[1]);die;
				};
				$re = $this->check_password($post,$userData);
				if(!$re){
					echo json_encode($this->arr[3]);die;
				};
				$this->arr[4]['id'] = $userData['merchant_id'];
				$this->arr[4]['name'] = $userData['m_name'];
				echo json_encode($this->arr[4]);die;
				break;
			case 'Admin':
				$userData = M('Admin')->where(array('is_del'=>0,'admin_name'=>$post['username']))->find();
				if(is_null($userData)){
					echo json_encode($this->arr[2]);die;
				};
				$re = $this->check_password($post,$userData);
				if(!$re){
					echo json_encode($this->arr[3]);die;
				};
				$this->arr[4]['id'] = $userData['admin_id'];
				$this->arr[4]['name'] = $userData['admin_name'];
				if(C('RBAC_SUPERADMIN') == $userData['admin_name']){
					$this->arr[4]['is_admin'] = 1;
				}else{
					$this->arr[4]['is_admin'] = 0;
				};
				echo json_encode($this->arr[4]);die;
				break;
			default:
				echo json_encode($this->arr[0]);die;
				break;
		};
	}

	/*
	* 获得子级信息
	* @param username password type
	*/
	public function get_son(){
		if(!IS_POST){
			echo $this->arr[0];die;
		};
		$post = I('post.');
		if(!isset($post['type']) || !isset($post['type']) || !isset($post['type'])){
			echo $this->arr[0];die;
		};
		$userData = M('Mer_son')->where(array('username'=>$post['username'],'is_del'=>0))->find();
		if(is_null($userData)){
			echo $this->arr[2];die;
		};
		$re = $this->check_password($post,$userData);
		if(!$re){
			echo $this->arr[3];die;
		};
		$this->arr[4]['id'] = $userData['id'];
		$this->arr[4]['name'] = $userData['username'];
		//获得对应系统的所有权限
		$node_data = M('Mer_node')->select();
		$cid = M('Mer_node')->where(array('name'=>'finance','level'=>1))->getField('id');
		$cids = $this->get_pid($node_data,$cid);
		//获得用户的所有权限
		$roles = M('Mer_son_role')->where(array('son_id'=>$userData['id']))->getField('role_id',true);
		$roles = implode(',',$roles);
		$nodes = M('Mer_role_node')->where(array('role_id'=>array('IN',$roles)))->getField('node_id',true);
		$nodes = array_unique($nodes);
		foreach($nodes as $k => $v){
			if(!in_array($v,$cids)){
				unset($nodes[$k]);
			};
		};
		//拼接好对应的数据结构
		$data = M('Mer_node')->where(array('id'=>array('IN',$nodes)))->select();
		$temp = array();
		foreach($data as $k => $v){
			if($v['level'] == 2){
				$temp[] = $v;
			};
		};
		foreach($data as $k => $v){
			foreach($temp as $a => $b){
				if($v['pid'] == $b['id']){
					$temp[$a]['controller'][] = $v;
				};
			};
		};
		foreach($data as $k => $v){
			foreach($temp as $a => $b){
				foreach($b['controller'] as $q => $w){
					if($v['pid'] == $w['pid']){
						$temp[$a]['controller'][$q]['action'][] = $v;
					};
				};
			};
		};
		$this->arr[4]['access'] = $temp;
		echo json_encode($this->arr[4]);die;

	}

	//获得所有子级节点
	public function get_pid($data,$cid){
		//存储所有cid的数组
		$temp = array();
		//遍历数据
		foreach($data as $k => $v){
			//如果pid等于cid
			if($v['pid'] == $cid){
				//把当前的cid压入数组
				$temp[] = $v['id'];
				//调用递归，查询子级内是否还有子级
				$temp = array_merge($temp,$this->get_pid($data,$v['id']));
			};
		};
		//返出结果
		return $temp;
	}

	//验证密码
	private function check_password($post,$userData){
		$password = md5(md5($post['username']).md5($post['password']));
		if($userData['password'] != $password){
			return FALSE;
		};
		return true;
	}
}

?>