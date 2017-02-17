<?php namespace Common\Model;
use Think\Model;

/**
 * 创建广告类型模型
 */
class AdverModel extends Model{
	protected $tableName="adver";
	
	protected $_validate = array(
     	array('adver_name','require','验证码必须！'), //默认情况下用正则进行验证
     	array('adver_url','require','验证码必须！'), //默认情况下用正则进行验证
     	array('adver_type_id','require','验证码必须！'), //默认情况下用正则进行验证
     	array('pic_src','require','验证码必须！'), //默认情况下用正则进行验证
   );
   
   
   
   public function store($data){
// 		dump($data);die;
	   	if(!$this->create($data)) return FALSE;
//		dump($this->data);die;
		$re = $this->add($this->data);
		return $re;
   }
   
   public function getData($adver_type_id){
   		if($adver_type_id){
   			$condition = array('is_del'=>0,'adver_type_id'=>$adver_type_id);
   		}else{
   			$condition = array('is_del'=>0);
   		}
	   	$data = $this->where($condition)->order('sort DESC')->select();
		return $data;
   }
   public function editData($data){
   		$data = $this->where(array('adver_id'=>$data))->find();
   		return $data;
   }
   public function edit($data){
   		if(!$this->create($data)) return FALSE;
		$re = $this->where(array('is_del'=>0))->save($data);
		return $re;
   }
   public function chenk_edit($data){
   		if(!$this->create($data)) return FALSE;
		$re = $this->where(array('adver_id'=>$data['adver_id']))->save($data);
		return $re;
   }
   
}
