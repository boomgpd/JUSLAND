<?php namespace Merchant\Model;
use Think\Model;
header('Content-Type: text/html; charset=utf-8');

/**
 * 创建商家套餐模型
 */
class Merchant_messageModel extends Model{
//	指定模型控制表名称
	protected $tableName="merchant_message";
	
//	自动验证属性
	protected $_validate = array(
     array('logo_yuan','require','标题必须填写'), 			// 验证该字字段是否为空
     array('company_des','require','添加时间必须存在'), 	
     array('sale_time','require','关键词必须填写'), //默认情况下用正则进行验证
     array('close_time','require','图片内容不能为空'), //默认情况下用正则进行验证
     array('merchant_id','require','商家主键不能为空'), //默认情况下用正则进行验证
   );


	//商家上线验证信息方法
	public function verify($id){
		//验证案例数据
		$case_data = M('Merchant_case')->where(array('is_del'=>0,'merchant_id'=>$id,'is_show'=>1))->select();
		if(count($case_data) < 4){
			$this->error = '案例少于四个不能上线';
			return FALSE;
		}else{
			foreach($case_data as $k => $v){
				foreach($v as $a => $b){
					if(empty($b)){
						$result = $this->dec($a);
						if($result){
							$this->error = '第'.($k+1).'个案例的'.$result.'未填写';
							return FALSE;
						};
					};
				};
			};
		};
		//验证套餐数据
		$combo_data = M('Merchant_combo')->where(array('is_del'=>0,'merchant_id'=>$id,'is_show'=>1))->select();
		if(count($combo_data) < 4){
			$this->error = '套餐少于四个不能上线';
			return FALSE;
		}else{
			foreach($combo_data as $k => $v){
				foreach($v as $a => $b){
					if(empty($b)){
						$result = $this->dec($a);
						if($result){
							$this->error = '第'.($k+1).'个套餐的'.$result.'未填写';
							return FALSE;
						};
					};
				};
			};
		};
		//验证主表和详情表
		$data = M('Merchant m')->where(array('m.is_del'=>0,'m.merchant_id'=>$id))->join('LEFT JOIN merchant_message mm ON m.merchant_id = mm.merchant_id')->find();
		foreach($data as $k => $v){
			if(empty($v)){
				$result = $this->dec($k);
				if($result){
					$this->error = $result.'未填写';
					return FALSE;
				};
			};
		};
		return true;
	}
	
	/**
	 * 创建修改商家基本信息方法
	 */
	 public function chenge($data){
	 	$data['merchant_id'] = $_SESSION['merchant_id'];
	 	if(!$this->create($data)) return FALSE;
		$oldData = $this->where(array('merchant_id'=>$_SESSION['merchant_id']))->find();
		if($oldData){
			$re = $this->where(array('merchant_message_id'=>$oldData['merchant_message_id']))->save($data);
		}else{
			$re = $this->add($data);
		}
		return TRUE;
	 }
	 
	
//	创建添加商家方法
	public function store($data){
		$data['addtime'] = time();
		$data['img_url'] = implode("|", $data['img_url']);
		$data['merchant_id'] = $_SESSION['merchant_id'];
		if(!$this->create($data)) return FALSE;
		$re = $this->add($data);
		return $re;
	}
	
	/**
	 * 创建获取商家案例方法
	 */
	 public function getData(){
	 	$data = $this->where(array('is_del'=>0,'merchant_id'=>$_SESSION['merchant_id']))->select();
		return $data;
	 }

	 //判断键名对应的信息
	 private function dec($k){
	 	switch ($k) {
			case 'm_name':
				$name = '商家名称';
				break;
			case 'leagal_person':
				$name = '商家联系人';
				break;
			case 'mobile_phone':
				$name = '联系人手机号';
				break;
			case 'phone':
				$name = '商家座机号码';
				break;
			case 'email':
				$name = '企业邮箱';
				break;
			case 'area':
				$name = '所在地区';
				break;
			case 'title':
				$name = '案例标题';
				break;
			case 'keyword':
				$name = '关键词';
				break;
			case 'img_url':
				$name = '图片';
				break;
			case 'title':
				$name = '套餐标题';
				break;
			case 'logo_fang':
				$name = '顶部方形logo';
				break;
			case 'logo_yuan':
				$name = '中间圆形logo';
				break;
			case 'logo_list':
				$name = '列表页logo';
				break;
			case 'img_list':
				$name = '商家列表图片';
				break;
			case 'm_name':
				$name = '商家名称';
				break;
			case 'company_des':
				$name = '公司简介';
				break;
			case 'sale_time':
				$name = '开始营业时间';
				break;
			case 'close_time':
				$name = '闭馆时间';
				break;
			default:
				$name = false;
				break;
		};
		return $name;
	 }
	
	
}
