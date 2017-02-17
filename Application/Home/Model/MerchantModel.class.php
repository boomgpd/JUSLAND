<?php namespace Home\Model;
use Think\Model;
class MerchantModel extends Model{
	protected $tableName="merchant";
	//	自动验证属性
	protected $_validate = array(
     array('m_name','','该公司已经注册',0,'unique',1), 		// 在新增的时候验证name字段是否唯一
     array('m_name','require','负责人字段必须填写'), 			// 验证该字字段是否为空
     array('leagal_person','require','负责人字段必须填写'), 	
     array('phone','','该座机号码已经注册',0,'unique',1),
     array('phone','/^((0\d{2,3}-\d{7,8})|(1[3584]\d{9}))$/','公司座机格式错误'), //判定该座机号码是否符合
     array('mobile_phone','/^(0|86|17951)?(13[0-9]|15[012356789]|17[678]|18[0-9]|14[57])[0-9]{8}$/','手机号格式错误'),
     array('mobile_phone','','该手机号码已经注册',0,'unique',1),
     array('email','','该邮箱已经注册',0,'unique',1),
     array('email','email','邮箱格式不正确'),
     array('area','require','地区必须填写'), //默认情况下用正则进行验证
   );
	/**
	 * 创建登录验证方法
	 */
	 public function check_login($data){
	 	/**
		 * 验证对应用户名是否存在
		 * 验证密码是否正确
		 */
	 	$table = ucfirst($data['identity']);
	 	switch ($table) {
	 		case 'Merchant':
	 			$name = 'm_name';
	 			break;
	 		case 'Photo':
	 			$name = 'p_name';
	 			break;
	 		case 'Hotel':
	 			$name = 'h_name';
	 			break;
	 		default:
	 			return array('type'=>102,'content'=>'用户名不存在');
	 			break;
	 	};
		 $oldData = M($table)->where(array($name=>$data['name']))->find();
		 if(!$oldData){
   			$oldData = $this->where(array('phone'=>$data['name']))->find();
   		};
		 if(!$oldData) return array('type'=>102,'content'=>'用户名不存在');
		 if($table == 'Merchant' && $oldData['is_apply'] != 1){
		 	return array('type'=>102,'content'=>'账号审核未通过');
		 }else if($table != 'Merchant' && $oldData['apply_type'] != 1){
		 	return array('type'=>102,'content'=>'账号审核未通过');
		 };
		 $new_pwd = md5(md5($oldData[$name]).md5($data['password']));
		 if($new_pwd != $oldData['password']) return array('type'=>103,'content'=>'密码输入错误');
		 unserialize($_SESSION);
		 $_SESSION[lcfirst($table).'_id'] = $oldData[lcfirst($table).'_id'];
		 return array('type'=>100,'content'=>'登录成功','urls'=>U("{$table}/index/index"));
	 }
	 
	 /**
	  * 创建编辑商家基本信息方法
	  */
	  public function editData($data){
	  	/**
		 * 1.判定商家信息是否合法
		 * 2.编辑信息
		 * 3.调用商家信息表方法，添加或者编辑数据
		 */
	  	$re = $this->create($data);
		if(!$re){
			return FALSE;
		}else{
			$re = $this->where(array('merchant_id'=>$_SESSION['merchant_id']))->save($this->data);
		}
		
	  }
	  /**
	   * 创建商家信息显示的方法
	   */
	  public function getData($data){
	  	if($data['merchant_merchant_id']==0){
	  		$re        = M('picture')->where(array('merchant_merchant_id'=>$data['merchant_merchant_id']))->find();
	  		$works_num = M('picture')->where(array('merchant_merchant_id'=>$data['merchant_merchant_id']))->count();
	  		$arr = array(
	  			'm_name'      =>$re['merchant_name'],
		  		'logo_head'   =>$re['merchant_logo'],
		  		'works_num'   =>$works_num
	  		);
	  	}else{
		  	$re        = $this->where(array('merchant_id'=>$data['merchant_merchant_id']))->find();
		  	$info      = M('merchant_message')->where(array('merchant_id'=>$data['merchant_merchant_id']))->find();
		  	$works_num = M('picture')->where(array('merchant_merchant_id'=>$data['merchant_merchant_id']))->count();
		  	$arr = array(
		  		'm_name'      =>$re['m_name'],
		  		'logo_head'   =>$info['logo_fang'],
		  		'works_num'   =>$works_num
		  	); 
	  	}
	  	return $arr;
	  }
	 
	 
	
}
