<?php namespace Common\Model;
use Think\Model;
header('Content-Type: text/html; charset=utf-8');
class Merchant_remarkModel extends Model{
//	指定模型控制表名称
	protected $tableName="merchant_remark";
	
//	自动验证属性
	protected $_validate = array(
     array('member_name','require','用户名称必须填写'),
     array('content','require','评论内容必须填写'), 
     array('space_num','require','空间评价必须填写'), 
     array('service_type','require','服务类型必须填写'), 
     array('star_num','require','服务评价必须填写'), 
//   array('admin_id','require','操作员主键必须存在'), 
     array('merchant_id','require','商家主键必须存在'), 
     array('addtime','require','添加时间必须存在'), 
   );
	
//	创建添加商家方法
	public function store($data){
		/**
		 * 1.将area字段的数组转化为字符串
		 * 2.将手机号后6位截取出来作为登录密码,同样用公司名称加上密码分别加密再拼接加密
		 * 3.验证每个字段是否符合要求
		 * 4.验证成功，添加进入数据库
		 */
		 $data['admin_id'] = $_SESSION['admin_id'];
		 $data['addtime'] = time();
		 $data['list_img'] = implode("|", $data['list_img']);
		if(!$this->create($data)) return FALSE;
//		dump($this->data);die;
		$re = $this->add($data);
//		dump($re);die;
		return $re;
	}

	/**
	  * 创建获取商家评论消息
	  */
	  public function getData($merchant_id){
	  	$data = $this->where(array('merchant_id'=>$merchant_id))->select();
		foreach($data as $k=>&$v){
			$v['addtime'] = date("Y-m-d H:i:s");
			$v['list_img'] = explode("|", $v['list_img']);
		}
		return $data;
	  }


	
}
