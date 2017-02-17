<?php namespace Common\Model;
use Think\Model;

/**
 * 创建广告类型模型
 */
class MemberModel extends Model{
	protected $tableName="member";
	
	protected $_validate = array(
      array('member_name','require','请填写用户名'),
      array('member_name','/[\D\s]/','用户名不能使用纯数字'),
      array('mamber_name','','已有相同的用户名',1,'unique',1),
      array('password','require','密码不能为空'),
      array('phone','require','手机号不能为空'),
      array('phone','','该手机已经注册',1,'unique',1),
      array('phone','/^(0|86|17951)?(13[0-9]|15[012356789]|17[678]|18[0-9]|14[57])[0-9]{8}$/','手机号格式错误'),

  	);

	//添加方法
	public function store($data){
		if($this->create($data)) return false;//验证失败
      	$data['password'] = md5(md5($data['member_name']).md5($data['password']));
		return $this->add($data);
	}

   
}
