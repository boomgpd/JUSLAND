<?php namespace Common\Model;
use Think\Model;
/**
 * 影楼分类表
 */
class HotelMakeModel extends Model{
	protected $tableName="hotel_make";
	
	protected $_validate = array(
	     array('make_name','require','姓名必须填写！'), 
	     array('make_phone','require','联系方式必须填写！'),
	     array('p_p_id','require','商品id必须填写！'),
	     array('addtime','require','添加时间必须填写！'),
	     array('is_date','require','添加时间必须填写！'),
   	);
   public function make($data){
   		$info = $this->where(array('p_p_id'=>$data['p_p_id'],'is_date'=>date('Y-m-d'),'make_phone'=>$data['make_phone']))->find();
   		if($info){
   			$re['bool'] = '您好，您以预约成功，请安心等待我们的工作人员联系您';
   			return $re;
   		}else{
	   		$data['addtime'] = time(); 
	   		$data['is_date'] = date('Y-m-d');
			if(!$this->create($data)) return FALSE;
			$re['info'] = $this->add($this->data);
   		}
		return $re;
   }
}