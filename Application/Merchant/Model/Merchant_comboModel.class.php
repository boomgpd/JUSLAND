<?php namespace Merchant\Model;
use Think\Model;
header('Content-Type: text/html; charset=utf-8');

/**
 * 创建商家套餐模型
 */
class Merchant_comboModel extends Model{
//	指定模型控制表名称
	protected $tableName="merchant_combo";
	
//	自动验证属性
	protected $_validate = array(
     array('title','require','标题必须填写'), 			// 验证该字字段是否为空
     array('addtime','require','添加时间必须存在'), 	
     array('keyword','require','关键词必须填写'), //默认情况下用正则进行验证
     array('img_url','require','图片内容不能为空'), //默认情况下用正则进行验证
     array('merchant_id','require','商家主键不能为空'), //默认情况下用正则进行验证
//   array('price','require','价格不能为空'), //默认情况下用正则进行验证
   );
	
//	创建添加商家方法
	public function store($data){
		$data['addtime'] = time();
		$data['img_url'] = implode("|", $data['img_url']);
		$data['merchant_id'] = $_SESSION['merchant_id'];
		if(!$this->create($data)) return FALSE;
		$re = $this->add($data);
		return $re;
	}
	
	/**
	 * 创建获取商家案例方法
	 */
	 public function getData($merchant_id,$show){
	 	if($show){
			$condition = array('is_del'=>0,'merchant_id'=>$merchant_id);
	 		$data = $this->order('sort')->where($condition)->select();
			
	 	}else{
	 		$condition = array('is_del'=>0,'merchant_id'=>$merchant_id,'is_show'=>1);
	 		$num = $this->where($condition)->count();
			if(!$num){
				$arr = $this->where(array('is_del'=>0,'merchant_id'=>$merchant_id))->select();
				$data = array();
				for($i=0;$i<4;$i++){
					$data[] = $arr[$i];
				}
			}else{
				$data = $this->order('sort')->where($condition)->select();
			}
	 	}

	 	
		foreach($data as $k=>&$v){
			$v['img_url'] = explode("|", $v['img_url']);
		}
		return $data;
	 }
	
	/**
	 * 创建编辑套餐排序显示方法
	 * 需要参数，键名为主键id，键值为排序,
	 * 现将该用户所有案例的显示字段更新为0，
	 * 再将现有数据主键的显示字段变为1，排序字段更新为指定数值
	 */
	public function edit_sort($data){
//		dump($data);die;
		$re = $this->where(array('merchant_id'=>$_SESSION['merchant_id']))->setField(array('is_show'=>0,'sort'=>0));
		if(!$re && $re != 0) return FALSE;
		foreach($data as $k=>$v){
			$re = $this->where(array('merchant_combo_id'=>$k))->save(array('is_show'=>1,'sort'=>$v));
			if(!$re) return FALSE;
		}
		
		return TRUE;
	}
	
	
}
