<?php namespace Common\Model;
use Think\Model;

/**
 * 创建广告类型模型
 */
class Adver_typeModel extends Model{
	protected $tableName="adver_type";
	
	protected $_validate = array(
     	array('adver_type_name','require','验证码必须！'), //默认情况下用正则进行验证
     
   );
   
   
   
   public function store($data){
// 		dump($data);die;
	   	if(!$this->create($data)) return FALSE;
//		dump($this->data);die;
		$re = $this->add($this->data);
		return $re;
   }
   
   public function getData(){
	   	$data = $this->where(array('is_del'=>0))->select();
		return $data;
   }
   
   public function edit($data){
// 		dump($data);die;
   		if(!$this->create($data)) return FALSE;
		$re = $this->where(array('is_del'=>0))->save($data);
		return $re;
   }
   
}
