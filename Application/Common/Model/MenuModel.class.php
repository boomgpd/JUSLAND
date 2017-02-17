<?php namespace Common\Model;
use Think\Model;
header('Content-Type: text/html; charset=utf-8');
/**
 * 创建二级导航的模型
 */
class MenuModel extends Model{
	
	protected $tableName="tow_menu";
	
	protected $_validate = array(
     array('menu_cont','require','用户名称必须填写'),
     array('menu_link','require','电话号码必须填写'), 
   );
	/**
	 * 创建添加的方法
	 */
	public function addMenu($data){
		$re = $this->create($data);
		if(!$re) return FALSE;
		$re = $this->add($this->$data);
		return $re;
	}
	/**
	 * 创建二级菜单显示的方法
	 */
	public function getData(){
		$re = $this->where(array('is_del'=>0))->select();
		return $re;
	}
	/**
	 * 创建编辑的方法
	 */
	 public function saveData($data){
	 	$re = $this->create($data);
		if(!$re) return FALSE;
		$re = $this->where(array('m_id'=>$data['m_id']))->save($this->$data);
		$arr = $this->where(array('m_id'=>$data['m_id']))->find();
		return $arr;
	 }
	 /**
	  * 创建删除的方法
	  */
	  public function delData($data){
	  	$arr = array(
			'is_del'=>1
		);
	  	$re = $this->where(array('m_id'=>$data['m_id']))->save(array('is_del'=>1));
		return $re;
	  }
}