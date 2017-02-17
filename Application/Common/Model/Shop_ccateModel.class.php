<?php
namespace Common\Model;
use Think\Model;

class Shop_ccateModel extends Model{
	
	protected $tableName = 'shop_choose_cate';

	protected $_vaildate = array(
		array('name','require','请填写分类名',1),
		array('category_id','require','请选择所属顶级分类',1),
		array('type','require','请选择显示规则',1),
		array('sort','require','请填写排序',1),
		array('img','require','请上传图片',1),
		array('cid','require','数据异常',1,'',2),
	);

	//添加
	public function store($post){
		if(!$this->create($post)) return FALSE;
		$name = $this->where(array('category_id'=>$post['category_id'],'name'=>$post['name'],'is_del'=>0))->find();
		if(!is_null($name)){
			$this->error = '已有相同的分类名';
			return FALSE;
		};
		$post['admin_id'] = $_SESSION['admin_id'];
		$post['addtime'] = time();
		return $this->add($post);
	}

	//编辑
	public function edit($post){
		if(!$this->create($post)) return FALSE;
		$re = $this->where(array('category_id'=>$post['category_id'],'name'=>$post['name'],'cid'=>array('NEQ',$post['cid']),'is_del'=>0))->find();
		if(!is_null($re)){
			$this->error = '已有相同的分类名';
			return FALSE;
		};
		$this->save($post);
		return true;
	}
}
?>