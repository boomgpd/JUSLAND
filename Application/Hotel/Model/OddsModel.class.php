<?php
namespace Hotel\Model;
use Think\Model;


//限时特惠模型
class OddsModel extends Model{
	
	protected $tableName = 'hotel_odds';
	
	protected $_validate = array(
		array('tnt','require','请填写特惠价格',1),
		array('start_year','require','请选择开始年份',1),
		array('start_month','require','请选择开始月份',1),
		array('start_day','require','请选择开始日期',1),
		array('end_year','require','请选择结束年份',1),
		array('end_month','require','请选择结束月份',1),
		array('end_day','require','请选择结束日期',1),
	);

	//添加方法
	public function store($post=NULL,$id){
		if(!$this->create($post)) return FALSE;
		$re = $this->verify($post,$id);
		if(!$re) return FALSE;
		$re['addtime'] = time();
		return $this->add($re);
	}

	//编辑
	public function edit($post=NULL,$id){
		if(!$this->create($post)) return FALSE;
		$re = $this->verify($post,$id);
		if(!$re) return FALSE;
		$this->save($re);
		return true;
	}

	//验证数据
	private function verify($post,$id){
		$price = M('hotel_product')->where(array('is_del'=>0,'p_p_id'=>$id))->getField('price');
		if($post['tnt'] > $price){
			$this->error = '特惠价不能大于商城价';
			return FALSE;
		};
		$now_time = time();
		$start_time = strtotime($post['start_year'].'-'.$post['start_month'].'-'.$post['start_day']);
		if($start_time < $now_time){
			$this->error = '开始时间选择错误';
			return FALSE;
		};
		$end_time = strtotime($post['end_year'].'-'.$post['end_month'].'-'.$post['end_day']);
		if($end_time < $start_time){
			$this->error = '结束时间不能在开始时间之前';
			return FALSE;
		};
		$data = array('p_p_id'=>$id,'tnt'=>$post['tnt'],'start_time'=>$start_time,'end_time'=>$end_time);
		return $data;
	}

	//获得数据
	public function getData($id){
		$data = M('hotel_odds')->where(array('is_del'=>0,'p_p_id'=>$id))->find();
		//获得开始时间和结束时间
		$start_time = date('Y-m-d',$data['start_time']);
		$end_time = date('Y-m-d',$data['end_time']);
		$one = strpos($start_time,'-');
		$two = strrpos($start_time,'-');
		$data['start_year'] = substr($start_time,0,$one);
		$data['start_month'] = ltrim(substr($start_time,$one+1,$two-$one-1),0);
		$data['start_day'] = ltrim(substr($start_time,$two+1),0);
		$one = strpos($end_time,'-');
		$two = strrpos($end_time,'-');
		$data['end_year'] = substr($end_time,0,$one);
		$data['end_month'] = ltrim(substr($end_time,$one+1,$two-$one-1),0);
		$data['end_day'] = ltrim(substr($end_time,$two+1),0);
		return $data;
	}

}
?>