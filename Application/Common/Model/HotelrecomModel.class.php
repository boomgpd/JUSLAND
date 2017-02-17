<?php namespace Common\Model;
use Think\Model;
/**
 * 影楼分类表
 */
class HotelrecomModel extends Model{
	protected $tableName="hotel_recom";
	
	protected $_validate = array(
     array('p_r_t_title','require','分类名称必须填写！'), 
     array('p_p_id','require','分类id必须勾选！'), 
     array('list_src','require','分类id必须勾选！'), 
   );
   /**
    * 创建添加推荐的方法
    */
    public function add($data){
    	if(!$this->create($data)) return false;//验证失败
    	$re = M('hotel_recom')->add($this->data);
		return $re;
    }
	/**
	 * 创建显示推荐的方法
	 */
	public function getData(){
		$re = $this->where(array('is_del'=>0))->select();
		foreach ($re as $key => &$value) {
			$arr = M('hotel_product')->where(array('p_p_id'=>$value['p_p_id']))->getField('hotel_id');
			$att = $value['p_p_id'];
			$value['p_p_id'] = M('hotel')->field('h_name,city')->where(array('hotel_id'=>$arr))->find();
			$value['p_p_id']['city'] = M('area')->where(array('area_id'=>$value['p_p_id']['city']))->getField('aname');
			$value['p_p_id']['id'] = $att;
		}
		return $re;
	}
}