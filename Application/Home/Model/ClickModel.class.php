<?php
namespace Home\Model;
use Think\Model;
/**
 * geyu
 * 8.11
 * 点赞记录模型
 */
class ClickModel extends Model {
	protected $tableName = "water_click";
	/**
	 * 创建点赞记录添加
	 * $data里面包含当前登陆用户ID、board_list——ID
	 */
	public function add_help($data) {
		/**
		 * 1.查询当前用户是否为该图片点过赞
		 * 2.如果有记录 则删除记录，更新点赞人数 返回对应数据
		 * 3.如果没有记录，则给点赞记录表添加点赞记录，返回对应数据，
		 */
		/*if ($data['board_list_id']) {
			$re = $this -> where(array('member_id' => $data['member_id'], 'board_list_id' => $data['board_list_id'])) -> find();
			if ($re) {
				$delete = $this -> where(array("water_click_id" => $re['water_click_id'])) -> delete();
				M('board_list')->where(array('board_list_id'=>$re['board_list_id']))->setInc('like_num','-1');
				$num = M('board_list')->where(array('board_list_id'=>$re['board_list_id']))->getField('like_num');
				$arr = array('type' => 0, 'contert' => '取消点赞', 'num' => $num);
			} else {
				$bool = M('water_click') -> add($data);
				M('board_list')->where(array('board_list_id'=>$data['board_list_id']))->setInc('like_num');
				$num = M('board_list')->where(array('board_list_id'=>$data['board_list_id']))->getField('like_num');
				$arr = array('type' => 1, 'contert' => '点赞完成', 'num' => $num);
			}
			return $arr;
		} else {*/
			$re = $this -> where(array('member_id' => $data['member_id'], 'picture_id' => $data['picture_id'])) -> find();
			if ($re) {
				$delete = $this -> where(array("water_click_id" => $re['water_click_id'])) -> delete();
				M('picture')->where(array('p_id'=>$re['picture_id']))->setInc('click_num','-1');
				$num = M('picture')->where(array('p_id'=>$re['picture_id']))->getField('click_num');
				$arr = array('type' => 0, 'contert' => '取消点赞', 'num' => $num);
			} else {
				$bool = $this->add($data);
				M('picture')->where(array('p_id'=>$data['picture_id']))->setInc('click_num');
				$num = M('picture')->where(array('p_id'=>$data['picture_id']))->getField('click_num');
				$arr = array('type' => 1, 'contert' => '点赞完成', 'num' => $num);
			}
			return $arr;
		/*}*/
	}

}
