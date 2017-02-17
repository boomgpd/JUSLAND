<?php
namespace Common\Model;
use Think\Model;

class Shop_usedModel extends Model{
	
	protected $tableName = 'shop_used';

	protected $_vaildate = array(
		array('title','require','请填写标题',1),
		array('money','require','请填写价钱',1),
		array('provience','require','请选择区域',1),
		array('city','require','请选择区域',1),
		array('type','require','请选择类型',1),
		array('linkman','require','请填写联系人',1),
		array('way','require','请填写联系方式',1),
		array('id','require','数据异常',1,'',2),
		array('address','require','详细地址必须填',1,'',2)
	);

	//添加
	public function store($post=NULL){
		if(!$this->create($post)) return FALSE;
		$post = $this->deal($post);
		if($post == FALSE) return FALSE;
		return $this->add($post);
	}

	//编辑
	public function edit($post=NULL){
		if(!$this->create($post)) return FALSE;
		$post = $this->deal($post,TRUE);
		if($post == FALSE) return FALSE;
		return $this->where(array('id'=>$post['id']))->save($post);
	}

	//处理数据方法
	private function deal($post,$bool=FALSE){
		if($post['type'] == 2 && empty($post['date'])){
			$this->error = '请填写出租时间';return FALSE;
		};
		if($post['type'] == 1) unset($post['date']);
		$post['edittime'] = time();
		if($bool == FALSE){
			$post['addtime'] = time();
			$post['merchant_id'] = $_SESSION['merchant_id'];
		}else{			
			$post['is_apply'] = 0;
		};
		return $post;
	}

	//获得详情页数据
	public function get_info($data){
		$data['provience'] = M('area')->where(array('area_id'=>$data['provience']))->getField('aname');
        $data['city'] = M('area')->where(array('area_id'=>$data['city']))->getField('aname');
        $data['showed'] = explode('|',$data['showed']);
        return $data;
	}

}
?>