<?php namespace Home\Model;
use Think\Model;
/**
 * geyu 
 * 8.12
 * 创建评论的模型
 */
class DiscussModel extends Model{
	protected $tableName="discuss";
	
	public function dis_mo($data){
		/**
		 * 首先判断用户是否登录，没有登录不可以评论
		 * 然后就添加评论  然后返回数据
		 */
		 if($_SESSION['member_id']){
		 	$re = $this->add($data);
			 if($re){
			 	$ren = $this->where(array('discuss_id'=>$re))->find();
				 $arr= array(
				 	'type'=>0,
				 	'content'=>'添加成功',
				 	'time'=>date("Y-m-d H:i:s",$ren['discuss_time']),
				 	'member_member_id'=>M('member')->where(array('member_id'=>$ren['member_member_id']))->getField('member_name'),
				 	'discuss_content'=>$ren['discuss_content']
				 );
			 }else{
			 	$arr= array(
					'type'=>1,
				 	'content'=>'添加失败'
				);
			 }
		 }else{
		 	$arr= array(
					'type'=>2,
				 	'content'=>'你还没有登录'
				);
		 }
		 return $arr;
	}
	public function dis_sect($data){
		$re = $this->where(array('board_list_board_id'=>$data))->select();
//		dump($re);die;
		$dis= array();
		foreach($re as $k => $v){
			$arr= array();
			$arr['dis']['member_name'] = M('member')->where(array('member_id'=>$v['member_member_id']))->getField('member_name');
			$arr['dis']['discuss_content'] = $v['discuss_content'];
			$arr['dis']['discuss_time'] = date("Y-m-d H:i:s",$v['discuss_time']);
			$dis[] = $arr;
		}
		return $dis;
	}
}