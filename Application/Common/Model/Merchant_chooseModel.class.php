<?php namespace Common\Model;
use Think\Model;
header('Content-Type: text/html; charset=utf-8');
class Merchant_chooseModel extends Model{
//	指定模型控制表名称
	protected $tableName="merchant_choose";
	
//	自动验证属性
	protected $_validate = array(
     array('merchant_id','require','请选择商家',1,'',1),
     array('rank','require','请选择排名',1),
     array('level','require','请选择等级',1),
     array('merchant_id','','该商家已在推荐',1,'unique',1),
     array('rank','','已有该排名推荐',1,'unique',1),
   );
   
   //添加
   public function store($data=NULL){
   	if(!$this->create($data)) return FALSE;
	if($this->count() >= 6){
		$this->error = '最多只能推荐6条';
		return FALSE;
	};
	return $this->add($data);
   }
   
   //编辑
	public function edit($data=NULL){
		if(!$this->create($data)) return FALSE;
		$re = $this->where(array('rank'=>$data['rank'],'merchant_choose_id'=>array('NEQ',$data['merchant_choose_id'])))->find();
		if(!empty($re)){
			$this->error = '已有相同的排名';
			return FALSE;
		};
		$this->save($data);
		return true;
	}
	
	//获得数据
	public function getData(){
		$data = $this->alias('mc')->join('merchant m ON mc.merchant_id = m.merchant_id AND m.is_del = 0 AND m.is_apply = 1')->join('LEFT JOIN merchant_message mm ON m.merchant_id = mm.merchant_id AND mm.is_show = 1')->order('rank ASC')->select();
		return $data;
	}
	
}
