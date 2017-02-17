<?php
namespace Home\Controller;
use Think\Controller;
class PictureController extends Controller {
	protected $picture;
	protected $board;
	protected $board_list;
	protected $discuss;
	protected $member;
	public function __construct (){
		parent::__construct();
		$this->picture=M('picture');
		$this->board=M('board');
		$this->board_list=M('board_list');
		$this->discuss=M('discuss');
		$this->member=M('member');
	}
	/*
	 * 创建用户转载本地图片的方法 
	 * */
	public function picture(){
		if(IS_POST){
			$data = I('post.');//接受post传过来的值
			$arr = array(//做成数组用来添加到数据库的值
				'collect_time' => time(),
				'title' => $data['title'],
				'picture_p_id' => $data['picture_p_id'],
				'board_board_id' => $data['board_board_id']
			);
			$bool=$this->board_list->add($arr);//添加
			if($bool){
				session('board_list_id',$bool);
				echo '<script type="text/javascript">alert("采集成功");history.back(0);</script>';
			}else{
				echo '<script type="text/javascript">alert("采集失败");</script>';
			}
		}
		/*
		 * 这个传向前台的是picture表
		 * */
		$info=$this->picture->select();
		/*
		 * 将picture表里的URL字符串分割
		 * */
		 $length= sizeof($info);
		for($i=0;$i<$length;$i++){
			$arr[$i]=explode('|',$info[$i]['url']);
		}
		$this->assign('info',$info);
		$this->assign('arr',$arr);
		/*
		 * 这个传向前台的是board表
		 * */
		$bool=$this->board->where(array('member_member_id' => $_SESSION['member_id']))->find();
		$this->assign('bool',$bool);
		/*
		 * 这个传向前台的是session();
		 * */
		$this->assign('session',$_SESSION);
		/*
		 * 这个传向前台的是discuss表
		 * */
		$bol=$this->discuss->where(array('discuss_id'=>$_SESSION['discuss_id']))->find();
		$this->assign('bol',$bol);
		/*
		 * 这个传向前台的是member表
		 * */
		$yon=$this->member->where(array('member_id'=>$_SESSION['member_id']))->find();
		$this->assign('yon',$yon);
		$this->display();
	}
	
}