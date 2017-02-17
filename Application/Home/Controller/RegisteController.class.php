<?php
namespace Home\Controller;
use Think\Controller;
use Common\Model\AreaModel;
use Common\Model\MerchantModel;
use Common\Model\PhotoModel;
use Common\Model\HotelModel;
use Common\Model\Diy_marrierModel;
use Common\Model\MemberModel;
use Think\Verify;

/**
 * 前台注册控制器
 * boom
 * 08/11
 */
class RegisteController extends CommonController {
	protected $member;
	protected $area;
	protected $merchant;
	protected $photo;
	protected $hotel;
	protected $verify;
	
	public function __construct(){
		parent::__construct();
		$this->member = new MemberModel;
		$this->area = new AreaModel; 
		$this->merchant = new MerchantModel;
		$this->photo = new PhotoModel;
		$this->hotel = new HotelModel;
		$this->diy_marrier = new Diy_marrierModel;
		$this->verify = new Verify;
	}

	/*
	 * 创建了一个注册用户的方法
	 * */
	public function member(){
		if(!IS_POST) return FALSE;//非POST请求返出
		$data = I('post.');//接收post传过来的值
		$verify = new \Think\Verify();
		$re = $verify->check($data['code']);//接受图形验证码传过来的值
		if(!$re){
		echo '<script type="text/javascript">alert("验证码错误");location.href = "'.U('index').'";</script>';die;
		};
		//执行添加方法
		$result = $this->member->store($data);
		if(!$result){
			$error = $this->member->getError();
			echo '<script type="text/javascript">alert("'.$error.'");location.href = "'.U('index').'";</script>';die;
		};
		echo '<script type="text/javascript">alert("注册成功");location.href = "'.U('Index/index').'";</script>';die;
		echo 2;die;
	}

	public function index(){
		for($i = 0;$i < 20;$i++){
			$arr = array('name'=>'a'.$i,'password'=>md5(md5('a'.$i).md5(123)),'phone'=>$this->get_phone(),'email'=>$this->get_email(),'is_apply'=>1);
			M('diy_marrier')->add($arr);
		}
		$this -> display();
	}

	public function get_phone(){
		$phone = '';
		for($i = 0;$i < 11;$i++){
			$phone .= rand(0,9);
		};
		$re = M('diy_marrier')->where(array('phone'=>$phone))->find();
		if(!is_null($re)){
			$phone = $this->get_phone();
		};
		return $phone;
	}

	public function get_email(){
		$phone = '';
		for($i = 0;$i < 10;$i++){
			$phone .= rand(0,9);
		};
		$phone .= '@qq.com';
		$re = M('diy_marrier')->where(array('email'=>$phone))->find();
		if(!is_null($re)){
			$phone = $this->get_phone();
		};
		return $phone;
	}

	/**
	 * 创建手机短信验证码方法
	 */
	public function yan(){
		if(!IS_AJAX) return FALSE;
    	$shouji=$_POST['name'];
        $sui=rand(1000,9999);
        session('sui',$sui);
		$arr = array('data'=>$sui);
		$this->ajaxReturn($arr);
    }
	
	/**
	 * 创建第三方申请
	 */
	 public function merchant(){
	 	/**
		 * 1.判定传输方式是不是post
		 * 2.调用模型内用户第三方添加方法
		 */
	 	if(!IS_POST)  return FALSE;
		$data = I('post.');
		//商家注册
		switch ($data['identity']) {
			case 'wedding':
				$table = 'merchant';
				break;
			case 'studio':
				$table = 'photo';
				break;
			case 'hotel':
				$table = 'hotel';
				break;
			default:
				return FALSE;die;
				break;
		};
		$re = $this->$table->store($data);
		if(!$re){
			$error = $this->$table->getError();
			echo '<script type="text/javascript">alert("'.$error.'");location.href = "'.U('index').'";</script>';
		}else{
			echo '<script type="text/javascript">alert("申请成功，工作人员会在12小时内给你回复，请保持手机畅通。");location.href = "'.U('index/index').'";</script>';
		}
	 }

	 //婚礼人注册
	 public function diy(){
		if(!IS_POST) return FALSE;//非POST请求返出
		$data = I('post.');
		$re = $this->verify -> check($data['code']);
		if(!$re){
			echo '<script type="text/javascript">alert("验证码错误");location.href = "'.U('index').'";</script>';die;
		};
		$result = $this->diy_marrier->store($data);
		if(!$result){
			$error = $this->diy_marrier->getError();
			echo '<script type="text/javascript">alert("'.$error.'");location.href = "'.U('index').'";</script>';
		}else{
			echo '<script type="text/javascript">alert("注册成功");location.href = "'.U('Home/Index/index').'";</script>';
		};
	}

	//提交验证
	public function post_deal(){
		if(!IS_AJAX) return false;
		$data = I('post.');
		if($data['data'][0]['name'] == 'identity'){
			$table = $this->get_table($data['table'],$data['data'][0]['value']);
		}else{
			$table = $this->get_table($data['table']);
		};
		if(!$table){
			return false;
		};
		unset($data['data'][0]);
		foreach($data['data'] as $k => $v){
			if($v['name'] != 'code'){
				if($table == 'Photo' && $v['name'] == 'm_name'){
					$v['name'] = 'p_name';
				}else if($table == 'Hotel' && $v['name'] == 'm_name'){
					$v['name'] = 'h_name';
				};
				$num = M($table) -> where(array($v['name'] => $v['value'])) -> find();
			}else{
				$result = $this->get_title($v,true);
			};
			if(!is_null($num)){
				$title = $this->get_title($v,$table);
				echo '已有相同的'.$title;die;
			};
		};
		echo '';die;
	}

	//获得回复文字
	private function get_title($arr,$table){
		if($arr['name'] == 'code'){
			$result = $this->verify -> check($arr['value']);
			if(!$result){
				echo '验证码错误';die;
			};
		};
		//如果是商家注册
		if($table == 'Merchant' || $table == 'Photo' || $table == 'Hotel'){
			if($arr['name'] == 'phone'){
				$title = '公司座机';
				return $title;
			};
		};
		//定义回复的文字
		switch ($arr['name']) {
			case 'name':
				$title = '用户名';
				break;
			case 'phone':
				$title = '手机号';
				break;
			case 'member_name':
				$title = '用户名';
				break;
			case 'm_name':
				$title = '公司名称';
				break;
			case 'leagal_person':
				$title = '联系人';
				break;
			case 'mobile_phone':
				$title = '联系人电话';
				break;
			case 'email':
				$title = '邮箱';
				break;
		};
		return $title;
	}

	//获得表名
	private function get_table($table,$identity=NULL){
		switch ($table) {
			case 'diy':
				$table = 'Diy_marrier';
				break;
			case 'user':
				$table = 'Member';
				break;
			case 'business':
				$table = 'Merchant';
				break;
			default:
				return FALSE;die;
				break;
		};
		//商家注册
		if($table == 'Merchant'){
			switch ($identity) {
				case 'wedding':
					$table = 'Merchant';
					break;
				case 'studio':
					$table = 'Photo';
					break;
				case 'hotel':
					$table = 'Hotel';
					break;
				default:
					echo '请选择身份';die;
					break;
			};
		};
		return $table;
	}

	//input表单ajax查询
	public function deal(){
		if(!IS_AJAX) return FALSE;//非AJAX请求返出
		$data = I('post.');
		if($data['table'] == 'business' && $data['name'] == 'identity'){
			echo '';die;
		};
		/**
	  	* 定义查询的表
	  	* diy 婚礼人
	  	* user 会员
	  	* business 商家
	  	*/ 
	  	$table = $this->get_table($data['table'],$data['identity']);
		if(!$table){
			echo FALSE;die;
		};
		if($table == 'Photo' && $data['name'] == 'm_name'){
			$data['name'] = 'p_name';
		}else if($table == 'Hotel' && $data['name'] == 'm_name'){
			$data['name'] = 'h_name';
		};
		$num = M($table) -> where(array($data['name'] => $data['value'])) -> find();
		if(!is_null($num)){
			$title = $this->get_title($data,$table);	
			echo '已有相同的'.$title;die;
		}else{
			echo '';die;
		};

	}
	
	/**
	  * 创建异步方法
	  */
	public function step_area(){
		if(!IS_AJAX) return FALSE;
		$pid = I('post.pid');
		$data = $this->area->getData($pid);
		$this->ajaxReturn($data);exit;
	}
	
}