<?php
namespace Common\Model;
use Think\Model;

//标签模型
class Shop_labelModel extends Model{
	
	protected $tableName = 'shop_label';

	
	protected $_validate = array(
		array('name','require','请输入标签名',1),
		array('category_id','require','请选择所属分类',1),
		array('id','require','数据异常',1,'',2),
	);

	//添加
	public function store($post=NULL){
		if(!$this->create($post)) return FALSE;
		$re = M('shop_category')->where(array('cid'=>$post['category_id'],'is_del'=>0))->getField('pid');
		if(is_null($re) || $re != 0){
			$this->error = '该分类不属于顶级分类';
			return FALSE;
		};
		$re = $this->where(array('name'=>$post['name'],'category_id'=>$post['category_id'],'is_del'=>0))->find();
		if(!is_null($re)){
			$this->error = '已有一样的标签所属该分类';
			return FALSE;
		};
		return $this->add($post);
	}

	//编辑
	public function edit($post){
		if(!$this->create($post)) return FALSE;
		$re = M('shop_category')->where(array('cid'=>$post['category_id'],'is_del'=>0))->getField('pid');
		if(is_null($re) || $re != 0){
			$this->error = '该分类不属于顶级分类';
			return FALSE;
		};
		$re = $this->where(array('id'=>array('NEQ',$post['id']),'name'=>$post['name'],'category_id'=>$post['category_id'],'is_del'=>0))->find();
		if(!is_null($re)){
			$this->error = '已有一样的标签所属该分类';
			return FALSE;
		};
		return $this->save($post);
	}

}
?>