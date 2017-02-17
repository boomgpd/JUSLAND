<?php namespace Admin\Model;
use Think\Model;
header('Content-Type: text/html; charset=utf-8');
class MerchantModel extends Model{
//	指定模型控制表名称
	protected $tableName="merchant";
	
//	创建审核更新字段方法
	public function audit($data){
		if($data['name'] == 'agree'){
			$re = $this->save(array('merchant_id'=>$data['id'],'is_apply'=>1));
		}else{
			$re = $this->save(array('merchant_id'=>$data['id'],'is_apply'=>2));
		}
		return $re;
	}
	
	/**
	 * 创建删除商家方法
	 */
	 public function del($id){
		$re = $this->where(array("merchant_id"=>$id))->save(array('is_del'=>1));
		return $re;
	 }
	 
	  
	  
	
}
