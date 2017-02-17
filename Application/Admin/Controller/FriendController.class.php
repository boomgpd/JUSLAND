<?php namespace Admin\Controller;
header('Content-Type: text/html; charset=utf-8');
use Think\Controller;
use Common\Model\FriendModel;

/**
 * 创建后台首页控制器
 * 16/08/03
 * boom
 */
class FriendController extends CommonController {
	protected $friend;
	
	public function __construct(){
		parent::__construct();
		$this->friend = new FriendModel;
		
	}
	public function index(){
		$data['friend'] = $this->friend->getData();
		$this->assign('data',$data);
		$this->display();
	}
	public function add_article(){
		$this->display();
	}
	public function check(){
		$data = I('post.');
		$re   = $this->friend->addData($data);
		if(!$re){
			echo '<script type="text/javascript">alert("添加失败");history.back(0);</script>';
		}else{
			echo '<script type="text/javascript">alert("添加成功");location.href="'.U('index').'";</script>';
	 	}
	}
	public function del_friend(){
		if(IS_AJAX){
	  		$data = I('post.');
			$arr = array(
				'friend_id'=>$data['del']
			);
			$re = $this->friend->delData($arr);
			$this->ajaxReturn($re);exit;
	  	}
	}
	public function save_friend(){
		if(IS_AJAX){
	 		$data = I('post.');
			$arr = array(
				"friend_id"=>$data['tid'],
				'friend_name'=>$data['friend_name'],
				'friend_url'=>$data['friend_url']
			);
			$re = $this->friend->saveData($arr);
			$this->ajaxReturn($re);exit;
		}
	}
}