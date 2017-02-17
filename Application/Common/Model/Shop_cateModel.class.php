<?php
namespace Common\Model;
use Think\Model;

class Shop_cateModel extends Model{
	protected $tableName = 'shop_category';

	private $son = 0;
	
	protected $_validate = array(
		array('cname','require','分类名不能为空',1),
		array('sort','require','排序不能为空',1),
		array('cid','require','数据异常',1,'',2),
	);

	//添加
	public function store($post){
		if(!$this->create($post)) return FALSE;
		if(!isset($post['pid']) || $post['pid'] == '' ){
			$this->error = '数据异常';
			return FALSE;
		};
		$post['admin_id'] = $_SESSION['admin_id'];
		$post['addtime'] = time();
		return $this->add($post);
	}

	//编辑
	public function edit($post){
		if(!$this->create($post)) return FALSE;
		if(!isset($post['type_id']) || $post['type_id'] == ''){
			$this->error = '数据异常';
			return FALSE;
		};
		$this->save($post);
		$re = $this->where(array('cid'=>$post['cid'],'is_del'=>0))->find();
		if(is_null($re)){
			$this->error = '数据异常';
			return FALSE;
		};
		return $re;
	}

	//获得自己和所有子级id
	public function get_son($id){
		$data = $this->where(array('is_del'=>0))->select();
		$cids = $this->myson($data,$id);
		$cids[] = $id;
		return $cids;
	}

	//获得子级
	private function myson($data,$id){
		$temp = array();
		foreach($data as $k => $v){
			if($v['pid'] == $id){
				$temp[] = $v['cid'];
				$temp = array_merge($temp,$this->myson($data,$v['cid']));
			};
		};
		return $temp;
	}

	//获得所有父级
	public function get_bread($id){
		$data = $this->where(array('cid'=>$id,'is_del'=>0))->find();
		if($data['pid'] != 0){
			$temp = $this->get_bread($data['pid']);
			$temp[] = $data['cid'];
		};
		return $temp;
	}

	//获得首页三级分类的数据
	// public function get_data($pid=0){
	// 	$data = $this->where(array('pid'=>$pid,'is_del'=>0))->select();
	// 	$this->son = $this->son + count($data);
	// 	if(!is_null($data) && $this->son > 0){
	// 		$this->son = $this->son - 1;
	// 		foreach($data as $k => $v){
	// 			$data[$k]['son'] = $this->get_data($v['cid']);
	// 		};
	// 	};
	// 	return $data;
	// }

	//获得首页三级分类的数据
	public function get_data(){
		$data['top'] = $this->where(array('pid'=>0,'is_del'=>0))->select();
		$data['son'] = array();
		foreach($data['top'] as $k => $v){
			$temp = $this->where(array('pid'=>$v['cid'],'is_del'=>0))->select();
			if(is_null($temp)) $data['son'][$k] = array();
			foreach($temp as $a => $b){
				$b['son'] = $this->where(array('pid'=>$b['cid'],'is_del'=>0))->select();
				$data['son'][$k][] = $b;
			};
		};
		return $data;
	}
}
?>