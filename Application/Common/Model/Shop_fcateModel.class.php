<?php
namespace Common\Model;
use Think\Model;

class Shop_fcateModel extends Model{
	
	protected $tableName = 'shop_fcategory';
	
	protected $_validate = array(
		array('fcname','require','请填写名称',1),
		array('category_id','require','请选择所属分类',1),
		array('sort','require','请填写排序',1),
		array('fcid','require','数据异常',1,'',2),
	);

	//添加
	public function store($post){
		if(!$this->create($post)) return FALSE;
		$num = $this->where(array('category_id'=>$post['category_id'],'is_del'=>0))->count();
		if($num >= 3){
			$this->error = '最多只能添加三条';
			return FALSE;
		};
		$fcname = $this->where(array('category_id'=>$post['category_id'],'fcname'=>$post['fcname'],'is_del'=>0))->find();
		if(!is_null($fcname)){
			$this->error = '该分类下已有相同的名称';
			return FALSE;
		};
		$post['admin_id'] = $_SESSION['admin_id'];
		$post['addtime'] = time();
		return $this->add($post);
	}

	//编辑
	public function edit($post){
		if(!$this->create($post)) return FALSE;
		$re = $this->where(array('category_id'=>$post['category_id'],'fcname'=>$post['fcname'],'fcid'=>array('NEQ',$post['fcid']),'is_del'=>0))->find();
		if(!is_null($re)){
			$this->error = '已有同样的名称';
			return FALSE;
		};
		$this->save($post);
		return true;
	}

	//获得首页数据
	public function get_data(){
		$data['cate'] = $this->where(array('is_del'=>0))->order('sort ASC')->select();
		$data['son'] = array();
		foreach($data['cate'] as $k => $v){
			$temp = M('Shop_fc_goods sfg')->where(array('sfg.fcid'=>$v['fcid'],'sfg.is_del'=>0,'sg.is_del'=>0))->join('__SHOP_GOODS__ sg ON sfg.gid = sg.gid')->select();
			if(!is_null($temp)){
				foreach($temp as $a => $b){
					$data['son'][$k][$b['sort']] = $b;
				};
			}else{
				$data['son'][$k] = array();
			}
		};
		return $data;
	}
}
?>