<?php namespace Home\Controller;
use Think\Controller;
use Home\Model\ClickModel;
class HelpController extends Controller {
	protected $water_click;
	public function __construct (){
		parent::__construct();
		$this->water_click= new ClickModel;
	}
	/*
	 * 葛
	 * 8月11号
	 * 点赞的方法
	 * */
	public function help(){
		if(!IS_AJAX) return FALSE;
		if(!$_SESSION['member_id']) return false;
		$id = I('post.');
		if($id['board_list_id']){
			$arr = array(
				'member_id'=>$_SESSION['member_id'],
				'board_list_id'=>$id['board_list_id'],
				'click_time'=> time()
			);
			$re = $this->water_click->add_help($arr);
			$this->ajaxReturn($re);exit;
		}else{
			$arr = array(
				'member_id'=>$_SESSION['member_id'],
				'picture_id'=> $id['P_id'],
				'click_time'=> time()
			);
			$re = $this->water_click->add_help($arr);
			/**
			 * 点赞成功，给数据库更新
			 */
			$this->ajaxReturn($re);exit;
		}
	}
}