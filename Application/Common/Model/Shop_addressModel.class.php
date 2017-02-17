<?php
namespace Common\Model;
use Think\Model;

//收货地址模型
class Shop_addressModel extends Model{
	
	protected $tableName = 'shop_address';
	
	protected $_validate = array(
		array('linkman','require','请填写联系人',1),
		array('phone','require','请填写手机号码',1),
		array('phone','/^1[3|4|5|8|7][0-9]\d{4,8}$/','手机号格式错误'),
		array('province','require','请选择省份',1),
		array('coding','require','邮编必须填写',2),
		array('coding','/[1-9]d{5}(?!d)/','邮编格式错误',2),
		array('city','require','请选择城市',1),
		array('details','require','请填写详细地址',1),
		array('id','require','数据异常',1,'',2),
	);

	//添加收货地址
	public function store($temp){
		//重新组合数据
		$post = array();
		foreach($temp as $k => $v){
			$post[$v['name']] = $v['value'];
		};
		if(!$this->create($post)) return FALSE;
		$post['member_id'] = $_SESSION['member_id'];
		$post['addtime'] = time();
		if($post['default'] == 1){
			$this->where(array('member_id'=>$_SESSION['member_id'],'is_del'=>0))->save(array('default'=>0));
		};
		$id = $this->add($post);
		$data = $this->where(array('id'=>$id))->find();
		return $this->get_data($data);
	}
	
	//添加收货地址
	public function ad_store($data){
		//重新组合数据
		if(!$this->create($data)) return FALSE;
		$this->data['member_id'] = $_SESSION['member_id'];
		$this->data['addtime'] = time();
		$id = $this->add($this->data);
		return $id;
	}
	
	//添加收货地址
	public function ad_edit($data){
		//重新组合数据
		if(!$this->create($data)) return FALSE;
		$this->data['member_id'] = $_SESSION['member_id'];
		$id = $this->save($this->data);
		return $id;
	}

	//编辑
	public function edit($temp){
		//重新组合数据
		$post = array();
		foreach($temp as $k => $v){
			$post[$v['name']] = $v['value'];
		};
		if(!$this->create($post)) return FALSE;
		$re = $this->where(array('member_id'=>$_SESSION['member_id'],'id'=>$post['id'],'is_del'=>0))->find();
		if(is_null($re)){
			$this->error = '非法操作';
			return FALSE;
		};
		$this->save($post);
		$data = $this->where(array('id'=>$post['id']))->find();
		return $this->get_data($data);
	}

	//处理收货地址
	public function get_data($data=NULL){
		$temp = array();
		//一条或多条
		if(is_null($data)){
			$data = $this->where(array('member_id'=>$_SESSION['member_id'],'is_del'=>0))->select();
			foreach($data as $k => $v){
				$t['linkman'] = $v['linkman'];
				$t['details'] = $v['details'];
				$t['default'] = $v['default'];
				$t['key'] = $v['id'];
				$t['province'] = M('Area')->where(array('area_id'=>$v['province'],'is_del'=>0))->getField('aname');
				$t['city'] = M('Area')->where(array('area_id'=>$v['city'],'is_del'=>0))->getField('aname');
				if($v['default'] == 1){
					$default = $t;
					continue;
				};
				$temp[] = $t;
			};
			if(isset($default)){
				array_unshift($temp,$default);
			};
		}else{
			$temp['linkman'] = $data['linkman'];
			$temp['details'] = $data['details'];
			$temp['key'] = $data['id'];
			$temp['default'] = $data['default'];
			$temp['province'] = M('Area')->where(array('area_id'=>$data['province'],'is_del'=>0))->getField('aname');
			$temp['city'] = M('Area')->where(array('area_id'=>$data['city'],'is_del'=>0))->getField('aname');
		};
//		dump($temp);die;
		return $temp;
	}

}
?>