<?php
namespace Home\Model;
use Think\Model;

class BoardModel extends Model {
	protected $tableName = "board";
	protected $_validate = array( array('board_name', 'require', '请填写画板的名称！'), array('member_member_id', 'require', '！'), );

	public function getData() {
		$data = $this -> where(array('member_member_id' => $_SESSION['member_id'])) -> select();
		return $data;
	}

	/*
	 * 添加画板的模型发
	 * */
	public function addData($data) {
		if (!$this -> create($data))
			return FALSE;
		$bool = $this -> add($this -> data);
		$re = $this -> where(array('board_id' => $bool)) -> find();
		$arr = array('board_name' => $re['board_name'], 'board_id' => $re['board_id'], 'member_member_id' => $re['member_member_id'], 'board_list_num' => M('board_list') -> where(array('board_board_id' => $bool)) -> count(), 'url1' => U('inside'), 'url2' => U('save'));
		return $arr;
	}

	/**
	 * 获取画板和对应画板列表信息
	 */
	public function boardData($arr) {
		/**
		 * 1.获取所有画板信息
		 * 2.获取对应画板对应的画板列表信息
		 * 3.获取画板列表对应picture的数据
		 * 4.将对应picture表的数据里的url变成
		 */
		if ($arr) {
			$lists = M('board_list') -> alias('a') -> join('__BOARD__ b ON a.board_board_id = b.board_id') -> where(array('board_board_id' => $arr['board_id'])) -> order('collect_time desc') -> select();
			$data = array();
			foreach ($lists as $k => $v) {
				$arr = array();
				$arr['box'] = M('picture') -> where(array('p_id' => $v['picture_p_id'])) -> find();
				$arr['box']['title'] = M('board_list') -> where(array('board_list_id' => $v['board_list_id'])) -> getField('title');
				$arr['box']['board_list_id'] = $v['board_list_id'];
				$arr['box']['board'] = M('board') -> where(array('board_id' => $v['board_board_id'])) -> find();
				$arr['box']['thumb'] = explode("|", $arr['box']['thumb']);
				$url = array();
				for ($i = 0; $i < $arr['box']['show_num']; $i++) {
					$url[] = $arr['box']['thumb'][$i];
				}
				$arr['box']['url'] = $url;
				$arr['member'] = M('member') -> where(array('member_id' => $v['member_member_id'])) -> find();
				$arr['water_click'] = M('water_click') -> where(array('member_id' => $v['member_member_id'])) -> select();
				$data[] = $arr;
			}
			return $data;
		} else {
			$data = $this -> where(array('member_member_id' => $_SESSION['member_id'])) -> select();
			$border_list = new BoardListModel;
			foreach ($data as $k => &$v) {
				$v['border_list'] = $border_list -> getData(array('board_id' => $v['board_id']));
			}
			return $data;
		}
	}

	public function ming($arr) {
		$data = $this -> where(array('member_member_id' => $arr['member_id'])) -> select();
		$border = array();
		foreach ($data as $key => $value) {
			$border[$key] = $value; 
			$border[$key]['list'] = M('board_list')->where(array('board_board_id'=>$value['board_id']))->getField('picture_p_id',TRUE);
			foreach ($border[$key]['list'] as $k => &$v) {
				$v = M('picture')->where(array('p_id'=>$v))->find();
				$v['thumb'] = explode("|", $v['thumb']);
			}
			// $border[$key]['member_id'] =  M('member')->where(array('member_id'=>$value['member_member_id']))->getField('member_name');
		}
		// $border_list = new BoardListModel;
		// foreach ($data as $k => &$v) {
		// 	$v['border_list'] = $border_list -> getData(array('board_id' => $v['board_id']));
		// }
		return $border;
	}

	public function bian($data) {
		$info = $this -> where(array('board_id' => $data['board_id'])) -> find();
		$arr = array('board_id' => $info['board_id'], 'board_name' => $info['board_name']);
		return $arr;
	}

	public function dele($data) {
		$arr = array($arr['re'] = $this -> where(array('board_id' => $data['board_id'])) -> delete(), $arr['bool'] = M('board_list') -> where(array('board_board_id' => $data['board_id'])) -> delete());
		return $arr;
	}

	/**
	 * 创建当前用户或非当前用户画板对应的瀑布流
	 */
	public function word($data) {
		$bool = $this -> where(array('member_member_id' => $data['member_member_id'])) -> getField('board_id', TRUE);
		$data = array();
		foreach ($bool as $key => &$value) {
			$value = M('board_list') -> alias('a') -> join('__BOARD__ b ON a.board_board_id = b.board_id') -> where(array('board_board_id' => $value)) -> order('collect_time desc') -> select();
			foreach ($value as $k => &$v) {
				$arr = array();
				$arr['box'] = M('picture') -> where(array('p_id' => $v['picture_p_id'])) -> find();
				$arr['box']['title'] = M('board_list') -> where(array('board_list_id' => $v['board_list_id'])) -> getField('title');
				$arr['box']['board_list_id'] = $v['board_list_id'];
				$arr['box']['board'] = M('board') -> where(array('board_id' => $v['board_board_id'])) -> find();
				$arr['box']['url'] = explode("|", $arr['box']['url']);
				$url = array();
				for ($i = 0; $i < $arr['box']['show_num']; $i++) {
					$url[] = $arr['box']['url'][$i];
				}
				$arr['box']['url'] = $url;
				$arr['member'] = M('member') -> where(array('member_id' => $v['member_member_id'])) -> find();
				$arr['water_click'] = M('water_click') -> where(array('member_id' => $v['member_member_id'])) -> select();
				$data[] = $arr;
			}
		}
		return $data;
	}
	public function board_in($data){
		$re = $this->where(array('board_id'=>$data))->find();
		$list_num   = M('board_list')->where(array('board_board_id'=>$re['board_id']))->count();
		$arr = array(
			'board_name' =>$re['board_name'],
			'list_num'   =>$list_num
		);
		return $arr;
	}

}
