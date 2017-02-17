<?php
namespace Common\Model;
use Think\Model;


//商家子级模型
class Mer_sonModel extends Model{
	
	protected $tableName = 'mer_son';
	
	protected $_validate = array(
		array('username','require','账号名不能为空',1),
		array('password','require','密码不能为空',1),
	);

	//添加
	public function store($post,$table){
		if(!$this->create($post)) return FALSE;
		switch (strtolower($table)) {
			case 'photo':
				$post['username'] = M('Photo')->where(array('photo_id'=>$_SESSION['photo_id'],'apply_type'=>1,'is_del'=>0))->getField('p_name').'_'.$post['username'];
				$post['type'] = 'photo';
				$post['pid'] = $_SESSION['photo_id'];
				break;
			case 'merchant':
				$post['username'] = M('Merchant')->where(array('merchant_id'=>$_SESSION['merchant_id'],'is_apply'=>1,'is_del'=>0))->getField('m_name').'_'.$post['username'];
				$post['type'] = 'merchant';
				$post['pid'] = $_SESSION['merchant_id'];
				break;
			case 'hotel':
				$post['username'] = M('Hotel')->where(array('hotel_id'=>$_SESSION['hotel_id'],'apply_type'=>1,'is_del'=>0))->getField('m_name').'_'.$post['username'];
				$post['type'] = 'hotel';
				$post['pid'] = $_SESSION['hotel_id'];
				break;
		};
		$re = $this->where(array('username'=>$post['username'],'is_del'=>0))->find();
		if(!is_NULL($re)){
			$this->error = '已有相同的用户名';
			return FALSE;
		};
		$post['password'] = md5(md5($post['username']).md5($post['possword']));
		$post['addtime'] = time();
		return $this->add($post);
	}
}
?>