<?php namespace Common\Model;
use Think\Model;
header('Content-Type: text/html; charset=utf-8');
/**
 * 创建站点配置的模型
 * geyu
 * 9.2
 */
class FriendModel extends Model{
	
	protected $tableName="friend";
	
	protected $_validate = array(
     array('friend_name','require','用户名称必须填写'),
     array('friend_url','require','电话号码必须填写'), 
   );
	/**
	 *创建添加的方法 
	 */
	 public function addData($data){
	 	$re = $this->create($data);
		if(!$re) return FALSE;
		$re = $this->add($this->$data);
		return $re;
	 } 
	 /**
	  * 创建显示的方法
	  */
	public function getData(){
		$re = $this->where(array('is_del'=>0))->select();
		return $re;
	}
	/**
	  * 创建删除的方法
	  */
	  public function delData($data){
	  	$re = $this->where(array('friend_id'=>$data['friend_id']))->save(array('is_del'=>1));
		return $re;
	  }
	  /**
	  * 创建编辑的方法
	  */
	  public function saveData($data){
		$re = $this->create($data);
		if(!$re) return FALSE;
		$re = $this->where(array('friend_id'=>$data['friend_id']))->save($this->$data);
		return $re;
	  }
}