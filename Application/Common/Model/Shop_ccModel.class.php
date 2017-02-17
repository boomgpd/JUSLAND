<?php
namespace Common\Model;
use Think\Model;

class Shop_ccModel extends Model{
	
	protected $tableName = 'shop_cc_goods';

	protected $_vaildate = array(
		array('gid','require','数据异常',1),
		array('cid','require','数据异常',1),
		array('sort','require','数据异常',1),
	);

	//添加
	public function store($post){
		if(!$this->create($post)) return FALSE;
		$type = M('Shop_choose_cate')->where(array('cid'=>$post['cid'],'is_del'=>0))->getField('type');
		if(is_null($type)){
			$this->error = '数据异常';
			return FALSE;
		};
		$num = $this->where(array('cid'=>$post['cid'],'is_del'=>0))->count();
		if($type == 1){
			if($num > 9){
				$this->error = '最多只能添加九个';
				return FALSE;
			};
			if($post['sort'] > 9 || $post['sort'] < 1){
				$this->error = '排序为一到九';
				return FALSE;
			};
		}else{
			if($num > 6){
				$this->error = '最多只能添加六个';
				return FALSE;
			};
			if($post['sort'] > 6 || $post['sort'] < 1){
				$this->error = '排序为一到六';
				return FALSE;
			};
			if(empty($post['img'])){
				$this->error = '请添加图片';
				return FALSE;
			};
		};
		$re = $this->where(array('cid'=>$post['cid'],'gid'=>$post['gid'],'is_del'=>0))->find();
		if(!is_null($re)){
			$this->error = '该推荐下已有此商品';
			return FALSE;
		};
		$re = $this->where(array('cid'=>$post['cid'],'sort'=>$post['sort'],'is_del'=>0))->find();
		if(!is_null($re)){
			$this->error = '该排序已有商品';
			return FALSE;
		};
		$post['admin_id'] = $_SESSION['admin_id'];
		$post['addtime'] = time();
		return $this->add($post);
	}

	//编辑
	public function edit($post){
		if(!$this->create($post)) return FALSE;
		if(empty($post['img'])){
			$this->error = '请添加图片';
			return FALSE;
		};
		$this->where(array('cid'=>$post['cid'],'gid'=>$post['gid'],'is_del'=>0))->save(array('img'=>$post['img']));
		return true;
	}
}
?>