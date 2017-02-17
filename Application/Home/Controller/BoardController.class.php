<?php
namespace Home\Controller;
use Think\Controller;
use Home\Model\BoardModel;
use Home\Model\PictureModel;
use Home\Model\BoardListModel;
use Home\Model\MemberModel;
use Home\Model\MerchantModel;
/**
 * geyu
 * 8.11
 * 创建画板具体页
 */
class BoardController extends CommonController {
	protected $board;
	protected $picture;
	protected $board_list;
	protected $member;
	protected $merchant;
	public function __construct() {
		parent::__construct();
		$this -> board = new BoardModel;
		$this -> board_list = new BoardListModel;
		$this -> picture = new PictureModel;
		$this -> member = new MemberModel;
		$this -> merchant = new MerchantModel;
	}

	/**
	 * 创建画板显示方法
	 */
	public function index() {
		$data['board'] = $this -> board -> ming($_GET);
		// dump($data);die;
		$data['member'] = $this -> member -> getData($_GET);
		$this -> assign('data', $data);
		$this -> assign('session', $_SESSION);
		$this -> display();
	}

	/*
	 * 创建更新用户的方法
	 * */
	public function board() {
		if (!IS_AJAX) return FALSE;
		$data = I('post.data');
		$arr = array('board_name' => $data, 'member_member_id' => $_SESSION['member_id']);
		$re = $this -> board -> addData($arr);
		$this -> ajaxReturn($re);exit ;
	}

	/**
	 * 创建收集页面
	 */
	public function single() {
		$arr = I('get.');
		if($arr['member_id']){
			$data['member'] = $this->member->getData();
		 	$data['picture'] = $this->board_list->getData($arr);
		}else if($arr['merchant_merchant_id']==0){
		 	$data['merchant'] = $this -> merchant -> getData($arr);
		 	$data['picture'] = $this->picture->getData($arr);
		}else if($arr['merchant_merchant_id']!=0){
		 	$data['merchant'] = $this -> merchant -> getData($arr);
		 	$data['picture'] = $this->picture->getData($arr);
		}
//		dump($data['picture']);die;
		$this -> assign('arr', $arr);
		$this -> assign('data', $data);
		$this -> display();
	}

	/**
	 * 创建board内的页面
	 */
	public function inside() {
		$arr = I('get.');
		$data['picture'] = $this -> board -> boardData($arr);
		$data['board'] = $this -> board -> board_in($arr['board_id']);
		$data['member'] = $this -> member -> getData();
		$this -> assign('data', $data);
		$this -> display();
	}

	/**
	 * 创建编辑采集的方法
	 */
	public function edit() {
		if (IS_POST) {
			$data = I('post.');
			$re = M('board_list') -> where(array('board_list_id' => $_GET['board_list_id'])) -> save(array('title' => $data['title']));
			if(!$re){
				echo '<script type="text/javascript">alert("更新失败");</script>';
			}else{
				echo '<script type="text/javascript">alert("更新成功");location.href = "'.U('home/board/index',array('member_id'=>$_SESSION['member_id'])).'"</script>';
			}
		}
		$data['board_list'] = $this -> board_list -> edit($_GET);
		$data['member'] = $this -> member -> getData();
//		dump($data['board_list']);die;
		$this -> assign('data', $data);
		$this -> display();
	}

	public function delete_de() {
		$re = $this -> board_list -> dele($_GET['board_list_id']);
		if(!$re){
			echo '<script type="text/javascript">alert("删除失败");</script>';
		}else{
			echo '<script type="text/javascript">alert("删除成功");location.href = "'.U('home/board/index',array('member_id'=>$_SESSION['member_id'])).'"</script>';
		}
	}
	/**
	 * 删除画板集编辑画板的方法
	 */
	public function save() {
		if (IS_POST) {
			$data = I('post.');
			$re = M('board') -> where(array('board_id' => $_GET['board_id'])) -> save(array('board_name' => $data['board_name']));
			if(!$re){
				echo '<script type="text/javascript">alert("更新失败");</script>';
			}else{
				echo '<script type="text/javascript">alert("更新成功");location.href = "'.U('home/board/index',array('member_id'=>$_SESSION['member_id'])).'"</script>';
			}
		}
		$data['board'] = $this -> board -> boardData();
		$data['member'] = $this -> member -> getData();
		$this -> assign('data', $data);
		$re = $this -> board -> bian($_GET);
		$this -> assign('re', $re);
		$this -> display();
	}

	public function delete() {
		$re = $this -> board -> dele($_GET);
		if(!$re){
			echo '<script type="text/javascript">alert("删除失败");</script>';
		}else{
			echo '<script type="text/javascript">alert("删除成功");location.href = "'.U('home/board/index',array('member_id'=>$_SESSION['member_id'])).'"</script>';
		}
	}

}
