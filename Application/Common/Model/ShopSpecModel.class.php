<?php
namespace Common\Model;
use Think\Model;
	/**
	 * 商城规格model
	 * 12.22
	 * 葛羽
	 */
class ShopSpecModel extends Model{
	protected $tableName = 'shop_spec_name';

	protected $_validate  = array(
		array('spec_name','require','请填写分类名',1),
		array('spec_name','','该公司已经注册',0,'unique',1),
	);
	/**
	 * 添加
	 */
	public function addData($data){
		$post = explode('|', $data['spec_name']);
		foreach ($post as $key => $value) {
			$data['addtime'] = time();
			$data['spec_name'] = $value;
			if(!$this->create($data)) return FALSE;
			$re = M('shop_spec_name')->add($this->data);
		} 
		return $re;
	}
	/**
	 * 获取规格
	 */
	public function getData($data){
		if($data['cid']){
			$arr = M('shop_category')->where(array('cid'=>$data['cid'],'is_del'=>0))->getField('spec_id');
			$list = str_replace("|",',',$arr);
			$arr = $this->where(array('is_del'=>0,'s_id'=>array('not in',$list)))->select();
		}else if($data['s_id']){
			$data = explode('|', $data['s_id']);
			$arr = array();
			foreach ($data as $key => $value) {
				$arr[] = $this->where(array('s_id'=>$value,'is_del'=>0))->find();
			}
		}
		return $arr;
	}
}