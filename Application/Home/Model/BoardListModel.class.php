<?php
namespace Home\Model;
use Think\Model;

class BoardListModel extends Model {
	protected $tableName = "board_list";
	protected $_validate = array( 
								array('board_board_id', 'require', '请选择你的画板！'), 
								array('title', 'require', '请填写title！'), 
								array('picture_p_id', 'require', '请重新采集！'), 
								array('collect_time', 'require', '没有获取到时间！'), 
							);
	//	获取picture后台上传的数据
	public function getData($data) {
		/**
		 * 1.获取到所有画板列表的内容
		 * 2.通过循环将对应字段压入返出数据内
		 */
		if (!$data) {
			$lists = M('board_list') -> alias('a') -> join('__BOARD__ b ON a.board_board_id = b.board_id') -> order('collect_time desc') -> select();
		} else if ($data['board_id'] && !$data['member_id']) {
			$lists = M('board_list') -> alias('a') -> join('__BOARD__ b ON a.board_board_id = b.board_id') -> where(array('board_board_id' => $data['board_id'])) -> order('collect_time desc') -> select();
		} else if ($data['keydorw']) {
			$keydorw['keyword'] = array('like', '%' . $data['keydorw'] . '%', );
			$lists = $this -> alias('a') -> join('__PICTURE__ b ON a.picture_p_id = b.p_id') -> join('__BOARD__ c ON a.board_board_id = c.board_id') -> where($keydorw) -> select();
		} else if($data['member_id']){
			$lists = M('board_list') -> alias('a') -> join('__BOARD__ b ON a.board_board_id = b.board_id') -> where(array('member_member_id' => $data['member_id'])) -> order('collect_time desc') -> select();
		}
		$data = array();
		foreach ($lists as $k => $v) {
			$arr = array();
			$arr['box'] = M('picture') -> where(array('p_id' => $v['picture_p_id'])) -> find();
			$arr['box']['title'] = $this -> where(array('board_list_id' => $v['board_list_id'])) -> getField('title');
			$arr['box']['board_list_id'] = $v['board_list_id'];
			$arr['box']['num'] = M('board_list') -> where(array('board_board_id' => $v['board_board_id'])) -> count();
			$arr['box']['board'] = M('board') -> where(array('board_id' => $v['board_board_id'])) -> find();
			$arr['box']['url'] = explode("|", $arr['box']['url']);
			$arr['box']['like_num'] = $v['like_num'];
			$arr['box']['collected_num'] = $v['collected_num'];
			$url = array();
			for ($i = 0; $i < $arr['box']['show_num']; $i++) {
				$url[] = $arr['box']['url'][$i];
			}
			$arr['box']['url'] = $url;
			$arr['member'] = M('member') -> where(array('member_id' => $v['member_member_id'])) -> find();
//			$arr['water_click'] = M('water_click') -> where(array('member_id' => $_SESSION['member_id'])) -> getField('board_list_id', TRUE);
			$arr['string'] = implode(',', $arr['water_click']);
			$data[] = $arr;
		}
//		dump($data);die;
		return $data;
	}
	
	/**
	 * 创建转载方法
	 */
	public function add_collect($data) {
		/*$board_list_id = $data['board_list_id'];
		unset($data['board_list_id']);*/
		if (!$this -> create($data)) return FALSE;
		$bool = $this -> add($this -> data);
		if ($bool) {
			M('picture')->where(array('p_id'=>$data['picture_p_id']))->setInc('collect_num');
			$arr = array('type' => 1, 'content' => '采集成功',);
		} else {
			$arr = array('type' => 0, 'content' => '采集失败');
		}
		return $arr;
	}

	public function board_list_($data) {
		$re = $this -> where(array('board_list_id' => $data)) -> find();
		$bool = M('picture') -> where(array('p_id' => $re['picture_p_id'])) -> find();
		$info = M('board') -> where(array('member_member_id' => $_SESSION['member_id'])) -> select();
		$arr = array('img_url' =>explode("|", $bool['url']), 'title' => $re['title'], 'board' => $info, 'p_id' => $bool['p_id']);
		return $arr;
	}

	public function photo($data) {
		$bool = $this -> where(array('board_list_id' => $data)) -> find();
		$board = M('board') -> where(array('board_id' => $bool['board_board_id'])) -> find();
		$mem = M('member') -> where(array('member_id' => $board['member_member_id'])) -> find();
		$info = M('picture') -> where(array('p_id' => $bool['picture_p_id'])) -> find();
		$click = M('water_click') -> where(array('board_list_id' => $data)) -> select();
		$like_num = M('water_click') -> where(array('board_list_id' => $data)) -> count();
		$att = array();
		foreach ($click as $key => $value) {
			$att[] = M('member') -> field('member_id,headimg,member_name') -> where(array('member_id' => $value['member_id'])) -> find();
		}
		$arr = array();
		$arr['url'] = explode("|", $info['url']);
		$arr['member'] = $att;
		$arr['like'] = $like_num;
		$arr['board_list_id'] = $bool['board_list_id'];
		$arr['member_name'] = $mem['member_name'];

		return $arr;
	}

	public function edit($data) {
		$re = $this -> where(array('board_list_id' => $data['board_list_id'])) -> find();
		$info = M('picture') -> where(array('p_id' => $re['picture_p_id'])) -> find();
		$arr = array('url' => explode("|", $info['url']), 'title' => $re['title'], 'board_list_id' => $re['board_list_id']);
		return $arr;
	}

	public function dele($data) {
		$bool = M('board_list') -> where(array('board_list_id' => $data['board_list_id'])) -> delete();
		return $bool;
	}

}
