<?php namespace Common\Model;
use Think\Model;
header('Content-Type: text/html; charset=utf-8');
/**
 * 创建站点配置的模型
 * geyu
 * 9.2
 */
class ConfigModel extends Model{
	
	protected $tableName="site_config";
	
	protected $_validate = array(
     array('attr_name','require','用户名称必须填写'),
     array('attr_value','require','电话号码必须填写'), 
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
	  	$re = $this->where(array('site_config_id'=>$data['site_config_id']))->save(array('is_del'=>1));
		return $re;
	  }
}