<?php namespace Common\Model;
use Think\Model;
header('Content-Type: text/html; charset=utf-8');
class PhotoModel extends Model{
//	指定模型控制表名称
	protected $tableName="photo";
	
//	自动验证属性
	protected $_validate = array(
//   array('p_name','','该公司已经注册',0,'unique',1), 		// 在新增的时候验证name字段是否唯一
     array('p_name','require','请填写公司名称'), 			// 验证该字字段是否为空
     array('leagal_person','require','负责人字段必须填写'), 	
//   array('phone','','该座机号码已经注册',0,'unique',1),
//   array('phone','/^(0[0-9]{2,3}\-)?([2-9][0-9]{6,7})+(\-[0-9]{1,4})?$/','公司座机格式错误'), //判定该座机号码是否符合
     array('mobile_phone','/^(0|86|17951)?(13[0-9]|15[012356789]|17[678]|18[0-9]|14[57])[0-9]{8}$/','手机号格式错误'),
     array('mobile_phone','','该手机号码已经注册',0,'unique',1),
     array('email','','该邮箱已经注册',0,'unique',1),
     array('email','email','邮箱格式不正确'),
     array('provience','require','地区必须填写'), //默认情况下用正则进行验证
     array('city','require','地区必须填写'), //默认情况下用正则进行验证
   );

	//添加
	public function store($data){
		/**
		 * 1.将area字段的数组转化为字符串
		 * 2.将手机号后6位截取出来作为登录密码,同样用公司名称加上密码分别加密再拼接加密
		 * 3.验证每个字段是否符合要求
		 * 4.验证成功，添加进入数据库
		 */
		$data['p_name'] = $data['m_name'];
		$data['apply_time'] = time();
		$data['password'] = md5(md5($data['m_name']).md5(substr($data['mobile_phone'], -6)));
		if(!$this->create($data)) return FALSE;
		$re = $this->add($data);
		return $re;
	}

	//创建审核更新字段方法
	public function audit($data){
		if($data['name'] == 'agree'){
			$re = $this->save(array('photo_id'=>$data['id'],'apply_type'=>1));
		}else{
			$re = $this->save(array('photo_id'=>$data['id'],'apply_type'=>2));
		};
		return $re;
	}
	
}
