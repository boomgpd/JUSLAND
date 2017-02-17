<?php namespace Common\Model;
use Think\Model;

class AreaModel extends Model{
	protected $tableName="area";
	
//	获取分类列表	
	public function getData($pid){
		/**
		 * 判定pid是否存在
		 * 如果存在，找到对应的子类
		 * 如果不存在，找到type为1的数据，is_del为0的数据
		 */
		 if(!$pid){
		 	$data = $this->where(array('type'=>1,'is_del'=>0))->select();
		 }else{
		 	$data = $this->where(array('is_del'=>0,'is_del'=>0,'pid'=>$pid))->select();
		 }
		 return $data;
	}
}
