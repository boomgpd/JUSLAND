<?php
namespace Photo\Model;
use Think\Model;

class ProductModel extends Model{
	
	protected $tableName = 'photo_product';
	
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
	    array('p_p_id','require','数据异常',1,'',2),
	);

	//添加方法
	public function store($post=NULL){
		$post['picture'] = implode(',',$post['picture']);
		$post['p_type'] = implode('|',$post['p_type']);
		if(!$this->create($post)) return FALSE;
		$level = M('Photo_category')->where(array('is_del'=>0,'p_c_id'=>$post['p_c_id'],'level'=>3))->find();
		if(is_null($level)){
			$this->error = '数据异常';
			return FALSE;
		};
		if($post['price'] > $post['original']){
			$this->error = '商城价不能大于原价';
			return FALSE;
		};
		//拼接数据
		$post['photo_id'] = $_SESSION['photo_id'];
		$post['addtime'] = time();
		M()->startTrans();
		$pid = $this->add($post);
		if(!$pid){
			M()->rollback();
			$this->error = '添加失败';
			return FALSE;
		};
		$re = $this->app($post['tab'],$pid);
		return $re;
	}

	//编辑
	public function edit($post){
		$post['picture'] = implode(',',$post['picture']);
		$post['p_type'] = implode('|',$post['p_type']);
		if(!$this->create($post)) return FALSE;
		M()->startTrans();
		$level = M('Photo_category')->where(array('is_del'=>0,'p_c_id'=>$post['p_c_id'],'level'=>3))->find();
		if(is_null($level)){
			$this->error = '数据异常';
			return FALSE;
		};
		if($post['price'] > $post['original']){
			$this->error = '商城价不能大于原价';
			return FALSE;
		};
		$this->save($post);
		//删除中间表数据
		M('Photo_product_tab')->where(array('p_p_id'=>$post['p_p_id']))->delete();
		$re = $this->app($post['tab'],$post['p_p_id']);
		return $re;
	}

	//添加tab表数据方法
	private function app($arr,$pid){
//		dump($arr);die;
//		if(!isset($arr['remark']) || !isset($arr['shoot']) || !isset($arr['sculpt']) || !isset($arr['other'])){
//			M()->rollback();
//			$this->error = '请保证数据的完整性';
//			return FALSE;
//		};
		//过滤数据
		foreach($arr as $k => $v){
//			if(empty($v['p_a_name'][0]) && empty($v['p_a_value'][0])){
//				M()->rollback();
//				$this->error = '至少添加一条'.$arr['msg'];
//				return FALSE;
//			};
			foreach($v['p_a_name'] as $a => $b){
//				if(empty($v['p_a_value'][$a])){
//					M()->rollback();
//					$this->error = '必须都填';
//					return FALSE;
//				}
				if(!empty($b) && !empty($v['p_a_value'][$a])){
					$temp = array('p_a_name'=>$b,'p_a_value'=>$v['p_a_value'][$a],'p_a_type'=>$v['type'][$a],'p_p_id'=>$pid);
					$re = M('Photo_product_tab')->add($temp);
					if(!$re){
						M()->rollback();
						$this->error = '添加失败';
						return FALSE;
					};
				};
			};
		};
		M()->commit();
		return true;
	}

	//获得编辑时数据
	public function getData($id){
		$data = $this->where(array('photo_id'=>$_SESSION['photo_id'],'is_del'=>0,'p_p_id'=>$id))->find();
		$data['picture'] = explode(',',$data['picture']);
		$tab = M('Photo_product_tab')->where(array('is_del'=>0,'p_p_id'=>$data['p_p_id']))->select();
		foreach($tab as $k => &$v){
			$find= M('photo_name')->where(array('photo_name_id'=>$v['p_a_name']))->find();
			$v['p_a_name'] = $find['name'];
			$v['photo_name_id'] = $find['photo_name_id'];
			$v['type'] = $find['type'];
			$data['tab'][$v['p_a_type']][] = $v;
		};
		$cate = M('Photo_category')->where(array('is_del'=>0,'p_c_id'=>$data['p_c_id'],'level'=>3))->find();
		$data['area'] = explode('|',$cate['p_area_name']);
		$data['type'] = explode('|',$cate['p_type_name']);
		$data['money'] = explode('|',$cate['p_money_name']);
		$data['p_type'] = explode('|',$data['p_type']);
		return $data;
	}

	//处理键名方法
	private function deal($k=NULL){
		$temp = array();
		switch($k){
			case 'remark':
				$temp['msg'] = '描述';
				$temp['type'] = 1;
				return $temp;
			case 'shoot':
				$temp['msg'] = '摄影';
				$temp['type'] = 2;
				return $temp;
			case 'sculpt':
				$temp['msg'] = '造型';
				$temp['type'] = 3;
				return $temp;
			case 'product':
				$temp['msg'] = '成品';
				$temp['type'] = 4;
				return $temp;
			case 'other':
				$temp['msg'] = '其他';
				$temp['type'] = 5;
				return $temp;
			default:
				return FALSE;
		};
	}
}
?>