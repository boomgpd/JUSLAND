<?php
namespace Hotel\Model;
use Think\Model;

class ProductModel extends Model{
	
	protected $tableName = 'hotel_product';
	
	protected $_validate = array(
	    array('p_name','require','请填写产品名称',1),
	    array('keyword','require','请添加产品关键词',1),
	    array('p_area','require','请选择拍摄地点',1),
	    array('p_type','require','请选择产品类型',1),
	    array('p_money','require','请选择产品价格区间',1),
	    array('picture','require','请添加商品图片',1),
	    array('list_img','require','请添加列表图片',1),
	    array('p_c_id','require','请选择分类',1),
	    array('original','require','请填写原价',1),
	    array('price','require','请填写商城价',1),
	    array('h_show','require','请填写商城价',1),
	    array('p_p_id','require','数据异常',1,'',2),
	);

	//添加方法
	public function store($post=NULL){
		$post['picture'] = implode(',',$post['picture']);
		$post['p_area'] = implode('|',$post['p_area']);
		$post['h_show'] = implode('|',$post['h_show']);
		if(!$this->create($post)) return FALSE;
		$level = M('hotel_category')->where(array('is_del'=>0,'p_c_id'=>$post['p_c_id'],'level'=>2))->find();
		if(is_null($level)){
			$this->error = '数据异常';
			return FALSE;
		};
		if($post['price'] > $post['original']){
			$this->error = '商城价不能大于原价';
			return FALSE;
		};
		//拼接数据
		$post['hotel_id'] = $_SESSION['hotel_id'];
		$post['addtime'] = time();
		M()->startTrans();
		$pid = $this->add($post);
		if(!$pid){
			M()->rollback();
			$this->error = '添加失败';
			return FALSE;
		};
		$re = $this->app($post,$pid);
		return $re;
	}

	//添加tab表数据方法
	private function app($arr,$pid){
		if(!isset($arr['hotel_value'])){
			M()->rollback();
			$this->error = '请保证数据的完整性';
			return FALSE;
		};
		//过滤数据
			$temp = array();
			foreach($arr['p_name_id'] as $a => $b){
				if(!empty($b) && !empty($arr['hotel_value'][$a])){
					$temp['hotel_value'] = $arr['hotel_value'][$a];
					$temp['p_p_id'] = $pid;
					$temp['p_name_id'] = $arr['p_name_id'][$a];
					$re = M('hotel_value')->add($temp);
					if(!$re){
						M()->rollback();
						$this->error = '添加失败';
						return FALSE;
					};
				};
			};
		M()->commit();
		return true;
	}
	//编辑
	public function edit($post){
		$post['picture'] = implode(',',$post['picture']);
		$post['p_area'] = implode('|',$post['p_area']);
		$post['h_show'] = implode('|',$post['h_show']);
		if(!$this->create($post)) return FALSE;
		M()->startTrans();
		$level = M('hotel_category')->where(array('is_del'=>0,'p_c_id'=>$post['p_c_id'],'level'=>2))->find();
		if(is_null($level)){
			$this->error = '数据异常';
			return FALSE;
		};
		if($post['price'] > $post['original']){
			$this->error = '商城价不能大于原价';
			return FALSE;
		};
		$re = $this->where(array('p_p_id'=>$post['p_p_id']))->save($post);
		//删除中间表数据
		$res = M('hotel_value')->where(array('p_p_id'=>$post['p_p_id']))->delete();
		$re = $this->app($post,$post['p_p_id']);
		return $re;
	}
	//获得编辑时数据
	public function getData($id){
		$data = $this->where(array('photo_id'=>$_SESSION['photo_id'],'is_del'=>0,'p_p_id'=>$id))->find();
		$data['picture'] = explode(',',$data['picture']);
		$data['h_show'] = explode(',',$data['h_show']);
		$tab = M('hotel_name')->where(array('is_del'=>0))->select();
		$data['tab'] = array();
		foreach($tab as $k => $v){
			$datas = M('hotel_value')->field('hotel_value,hotel_value_id')->where(array('p_p_id'=>$id,'p_name_id'=>$v['p_p_a_id']))->find();
			$datas['name']= $v['p_a_name'];
			$datas['id']= $v['p_p_a_id'];
			$data['tab'][] = $datas;
		};
		$cate = M('hotel_category')->where(array('is_del'=>0,'p_c_id'=>$data['p_c_id'],'level'=>2))->find();
		$data['area'] = explode('|',$cate['p_area_name']);
		$data['type'] = explode('|',$cate['p_type_name']);
		$data['money'] = explode('|',$cate['p_money_name']);
		$data['p_area'] = explode('|',$data['p_area']);
		$data['p_c_id'] = $cate['p_c_id'];
		$data['cname'] = $cate['cname'];
		return $data;
	}

}
?>