<?php
namespace Home\Controller;
use Think\Controller;
class DiscussController extends Controller {
	protected $picture;
	protected $board;
	protected $discuss;
	public function __construct (){
		parent::__construct();
		$this->picture=M('picture');
		$this->board=M('board');
		$this->discuss=M('discuss');
	}
	/*
	 * 创建一个用户给管理员评论的方法
	 * */
	public function discussg(){
		$data = I('post.');
		$arr= array(
			'discuss_time' => time(),
			'discuss_content' => $data['discuss_content'],
			'member_member_id' => $data['member_member_id'],
			'board_board_id' => '评论管理员的'
		);
		$bool=$this->discuss->add($arr);
		if($bool){
			session('discuss_id',$bool);
			echo '<script type="text/javascript"> history.back(0);</script>';
		}else{
			echo '<script type="text/javascript">alert("评论失败");history.back(0);</script>';
		}
		
	}
	/*
	 * 创建一个用户给用户评论的方法
	 * */
//	public function discussy(){
//		$data = I('post.');
//		$arr= array(
//			'discuss_time' => time(),
//			'discuss_cintent' => $data['discuss_cintent'],
//			'member_member_id' => $data['member_member_id'],
//			'board_board_id' => '评论用户的'
//		);
//	}
}