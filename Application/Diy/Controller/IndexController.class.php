<?php
namespace Diy\Controller;
use Think\Controller;
use Diy\Model\Diy_marrierModel;
/*
*婚礼人首页控制器
*/
class IndexController extends Controller{

	private $db;//diy_marrier表句柄
	private $id;//婚礼人id
	public function __construct(){
		//没有登录
		if(!isset($_SESSION['diy_marrier_id'])){
			$this->redirect('Home/Index/index');
		};
		//获得对应的id
		$this->id = $_SESSION['diy_marrier_id'];
		parent::__construct();
		$this->db = new Diy_marrierModel;
	}

	public function index(){
		//定义切换状态的提示消息
		$status = $this->db->join('LEFT JOIN diy_marrier_message ON diy_marrier.diy_marrier_id = diy_marrier_message.diy_marrier_id')->where(array('diy_marrier.diy_marrier_id'=>$this->id))->getField('status');
		if($status){
			$msg = '是否要切换成下线状态？切换后将搜索不到您';
		}else{
			$msg = '是否要切换成上线状态？请确保信息的完整性，否则将搜索不到您';
		};
		$this->assign('msg',$msg);
		$this->display();
	}

	//欢迎
	public function welcome(){
		echo '欢迎';
	}

	//个人信息
	public function info(){
		$data = $this->db->join('diy_marrier_message ON diy_marrier.diy_marrier_id = diy_marrier_message.diy_marrier_id')->where(array('diy_marrier.diy_marrier_id'=>$this->id))->find();
		$data['showed'] = explode(',',$data['showed']);
		$data['remark'] = explode('|',$data['remark']);
		$this->assign('data',$data);
		$this->display();
	}

	//编辑个人信息
	public function edit_basic(){
		if(IS_POST){
			$data = I('post.');
			//存入id 方便save
			$data['diy_marrier_id'] = $this->id;
			//执行编辑方法
			$result = $this->db->edit_basic($data);
			if(!$result){
				echo '<script type="text/javascript">alert("'.$this->db->getError().'");location.href = "'.U('edit_basic').'";</script>';die;
			};
			echo '<script type="text/javascript">alert("修改成功,上线后才能被查看");parent.location.href = "'.U('index').'";</script>';die;
		};
		//获得婚礼人表和婚礼人详细信息表数据
		$data = $this->db->getData($this->id);
		$this->assign('data',$data);
		//分配婚礼人类型 
		$typeData = M('Diy_marrier_type')->select();
		$this->assign('typeData',$typeData);
		//分配城市数据
		$areaData = M('Area')->where(array('pid'=>0))->select();
		$this->assign('areaData',$areaData);
		//判断是否有保存的市区，如有则用对应的省份查询市，county同理
		if(isset($data['address'][1])){
			$city = D('Area')->getData($data['address'][0]);
		}else{
			$city = D('Area')->getData($areaData[0]['area_id']);
		};
		$this->assign('city',$city);
		if(isset($data['address'][2])){
			$county = D('Area')->getData($data['address'][1]);
		}else{
			$county = D('Area')->getData($city[0]['area_id']);
		};
		$this->assign('county',$county);
		$this->display();
	}

	//编辑职业信息
	public function edit_career(){
		if(IS_POST){
			//获得POST数据
			$data = I('post.');
			$data['diy_marrier_id'] = $this->id;
			$result = $this->db->edit_career($data);
			if(!$result){
				echo '<script type="text/javascript">alert("'.$this->db->getError().'");location.href = "'.U('edit_career').'";</script>';die;
			};
			echo '<script type="text/javascript">alert("修改成功,上线后才能被查看");pareant.location.href = "'.U('index').'";</script>';die;
		};
		//获得婚礼人表和婚礼人详细信息表数据
		$data = $this->db->getData($this->id);
		$this->assign('data',$data);
		$this->display();
	}

	//修改密码
	public function change_pwd(){
		if(IS_POST){
			$data = I('post.');
			if(empty($data['one_pwd']) || empty($data['two_pwd'])){
				echo '<script type="text/javascript">alert("请输入新密码");location.href = "'.U('change_pwd').'";</script>';die;
			};
			$userData = $this->db->where(array('diy_marrier_id'=>$this->id))->find();
			//原密码不对
			$old_pwd = md5(md5($userData['name']).md5($data['old_pwd']));
			if($old_pwd != $userData['password']){
				echo '<script type="text/javascript">alert("旧密码错误");location.href = "'.U('change_pwd').'";</script>';die;
			};
			//新密码不对
			if($data['one_pwd'] != $data['two_pwd']){
				echo '<script type="text/javascript">alert("两次密码不一致");location.href = "'.U('change_pwd').'";</script>';die;
			};
			$password = md5(md5($userData['name']).md5($data['two_pwd']));
			$this->db->where(array('diy_marrier_id'=>$this->id))->save(array('password'=>$password));
			echo '<script type="text/javascript">alert("修改密码成功");parent.location.href = "'.U('index').'";</script>';die;
		};
		$this->display();
	}

	//城市联动
	public function linkage(){
		if(!IS_AJAX) return FALSE;
		$pid = I('post.pid',0,'intval');
		$level = I('post.level',0,'intval');
		if($level){
			$data['city'] = D('Area')->getData($pid);
			$data['county'] = D('Area')->getData($data['city'][0]['area_id']);
		}else{
			$data = D('Area')->getData($pid);
		};
		$this->ajaxReturn($data);die;
	}

	//图片上传
	public function upload(){
	 	$name = I('get.name');
	 	$upload = new \Think\Upload();// 实例化上传类
	    $upload->maxSize   =     3145728 ;// 设置附件上传大小
	    $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
	    $upload->rootPath  =     __ROOT__; // 设置附件上传根目录
	    $upload->savePath  =     $name.'/'; // 设置附件上传（子）目录
	    // 上传文件 
	    $info   =   $upload->upload();
	    if (empty($info)) {
	        echo json_encode($this->error());exit;
	    } else {
	        //上传成功，把上传好的信息返给js
	        $data = $info['Filedata']['savepath'].$info['Filedata']['savename'];
	        echo json_encode($data);exit;
	    }
	 }

	 //删除文件
	 public function del(){
	 	if(!IS_AJAX) return FALSE;
	 	$path = I('get.path');
	 	$path = './Uploads/'.$path;
	 	$result = unlink($path);
	 }

	//退出登录
	public function out(){
		unset($_SESSION['diy_marrier_id']);
		unset($_SESSION['diy_marrier_name']);
		$this->redirect('Home/Index/index');
	}

	//切换状态
	public function cut(){
		$data = $this->db->join(' LEFT JOIN diy_marrier_message ON diy_marrier.diy_marrier_id = diy_marrier_message.diy_marrier_id')->where(array('diy_marrier.diy_marrier_id'=>$this->id))->find();
		if($data['status']){
			M('Diy_marrier_message')->where(array('diy_marrier_id'=>$this->id))->save(array('status'=>0));
			$msg = '下线';
		}else{
			M('Diy_marrier_message')->where(array('diy_marrier_id'=>$this->id))->save(array('status'=>1));;
			$msg = '上线';
		};
		echo '<script type="text/javascript">alert("切换状态成功,当前状态为'.$msg.'");parent.location.href = "'.U('index').'";</script>';
	}



}

?>