<?php namespace Common\Model;
use Think\Model;
header('Content-Type: text/html; charset=utf-8');
/**
 * 创建二级导航的模型
 */
class Activity_labelModel extends Model{
	
	protected $tableName="activity_label";
	
	protected $_validate = array(
     array('a_l_name','require','名称必须填写'),
     array('a_l_desc','require','描述必须填写'), 
     array('a_l_img','require','图片必须上传'), 
     array('type','require','标签类型必须勾选'), 
   );
   public function addData($data){
   		if(!$this->create($data)) return FALSE;
		$re = M('activity_label')->add($data);
		return $re;
   }
   public function edit($data){
   		$arr = array(
			'a_l_name'=>$data['a_l_name'],
			'a_l_desc'=>$data['a_l_desc'],
			'a_l_img'=>$data['a_l_img']
		);
   		if(!$this->create($arr)) return FALSE;
		$re = M('activity_label')->where(array('a_l_id'=>$data['a_l_id']))->save($this->data);
		return $re;
   }
}