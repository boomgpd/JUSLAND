<?php
namespace Home\Controller;
use Think\Controller;
use Merchant\Model\Merchant_caseModel;
use Merchant\Model\Merchant_comboModel;
use Merchant\Model\Merchant_messageModel;
use Common\Model\Merchant_remarkModel;
use Common\Model\Merchant_discussModel;
use Common\Model\BananerModel;
use Common\Model\AdverModel;
use Common\Model\BespokeModel;

/**
 * 商家店铺管理控制器
 * boom
 * 08/12
 */
class MerchantController extends CommonController {
	protected $merchant_case;
	protected $merchant_combo;
	protected $merchant_message;
	protected $merchant_remark;
	protected $merchant_discuss;
	protected $bananer;
	protected $adver;
	protected $bespoke;

	public function __construct() {
		parent::__construct();
		$this -> merchant_case = new Merchant_caseModel;
		$this -> merchant_combo = new Merchant_comboModel;
		$this -> merchant_message = new Merchant_messageModel;
		$this -> merchant_remark = new Merchant_remarkModel;
		$this -> merchant_discuss = new Merchant_discussModel;
		$this -> bananer = new BananerModel;
		$this -> adver = new AdverModel;
		$this -> bespoke = new BespokeModel;
	}

	/**
	 * 创建店铺装修主页面
	 */
	public function index() {
		$merchant_id = I('get.merchant_id');
		
		if(IS_POST){
			$data = I('post.');
			$re   = $this->bespoke->store($data);
			if(!$re){
				echo '<script type="text/javascript">alert("申请失败");location.href = "'.U('case_show').'"</script>';
			}else{
				echo '<script type="text/javascript">alert("申请成功，工作人员会在12小时内与您取得联系，请保持手机畅通；");location.href = "'.U('index',array('merchant_id'=>$merchant_id)).'"</script>';
			}
		}
		
		/**
		 * 1.获取商户表信息
		 * 2.获取商家案例信息
		 * 3.获取商家套餐信息
		 * 4.商家问答信息
		 * 5.会员点评信息（有交易记录的可以评论，后台可以单独添加）
		 * 6.获取用户问答信息(后台可以独立添加)
		 */
		
		$data = array();
		$data['merchant'] = M('merchant') -> where(array('merchant_id' => $merchant_id)) -> find();
		$data['merchant_message'] = M('merchant_message') -> where(array('merchant_id' => $merchant_id)) -> find();
		$data['merchant_case'] = $this -> merchant_case -> getData($merchant_id);
		$data['merchant_combo'] = $this -> merchant_combo -> getData($merchant_id);
		$data['merchant_remark'] = $this -> merchant_remark -> getData($merchant_id);
		$data['merchant_discuss'] = $this -> merchant_discuss -> getData($merchant_id);
		$data['member'] = M('member')->where(array('member_id'=>$_SESSION['member_id']))->find();
		$this -> assign('data', $data);
		$remark_num = count($data['merchant_remark']);
		$this -> assign('remark_num', $remark_num);
		
		/**
		 * 获取轮播图信息
		 * 获取视屏信息
		 * 获取广告位图片
		 */
		$type_id = M('bananer_type') -> where(array('type_name' => '商家主页')) -> getField('bananer_type_id');
		$bananer = $this -> bananer -> order(array('sort' => 'ASC', 'addtime' => 'DESC')) -> getData($type_id);
		$this -> assign('bananer', $bananer);

		$video = M('merchant_video') -> order(array('addtime DESC')) -> where(array('is_del' => 0, 'is_show' => 1)) -> find();
		$this -> assign('video', $video);
		$adver_type_id = M('adver_type') -> where(array('adver_name' => '商家主页广告')) -> getField('adver_type_id');
		$adver = $this -> adver -> where(array('is_del' => 0)) -> find();
		$this -> assign('adver', $adver);
		$this -> display();
	}

	/**
	 * 创建商家列表页
	 */
	public function list_page() {
		/**
		 * 获取公司信息和公司方形logo和列表图
		 */
		$provience_id = M('area')->where(array('area_id'=>$_SESSION['location_city_id']))->getField('pid');
		$special_city = M('area')->where(array('aname'=>array('in','北京市,天津市,重庆市,上海市')))->getField('area_id',true);
		$data = M('merchant') -> alias('a') -> join('__MERCHANT_MESSAGE__ b ON a.merchant_id = b.merchant_id AND a.is_apply = 1 AND a.is_del = 0 AND b.is_show = 1') -> field('a.merchant_id,b.logo_fang,b.img_list,a.provience,a.city,b.server_prices_id,b.logo_list') -> select();
		/**
		 * 判定是否有筛选条件
		 */
		$get_data = I('get.');
		$this->assign('get_data',$get_data);
//		if($get_data['price_id'])
		foreach($data as $k=>$v){
			if(!empty($get_data['q']) && strpos($v['m_name'],$get_data['q']) === FALSE){//存在搜索并且搜索不到
				unset($data[$k]);
				continue;
			};
			$data[$k]['server_prices_id'] = explode('|',$v['server_prices_id']);
	 		if($v['provience'] != $_SESSION['location_city_id']){
//	 			不属于该省市的删掉
	 			unset($data[$k]);
	 		}
//	 			不属于该服务区间的删掉
			if($get_data['price_id'] && !in_array($get_data['price_id'],$data[$k]['server_prices_id'])){
				unset($data[$k]);
			}
//				不属于该市区的删掉
			if($get_data['area_id'] && !in_array($get_data['area_id'],$data[$k]['area'])){
				unset($data[$k]);
			}
		}
		$this -> assign('data', $data);
//		获取服务价格区间
		$price = M('merchant_server_price')->select();
		$this->assign('price',$price);
		
//		获取包含城市
//		$city = M('area')->where(array('pid'=>$provience_id))->select();
//		dump($provience_id);
		$part = array();
		if($provience_id==0){
			$provience_id = $_SESSION['location_city_id'];
			$part = M('area')->where(array('pid'=>$provience_id))->select();
//			foreach($city as $k=>$v){
//				$temp = M('area')->where(array('pid'=>$v['area_id']))->select();
//				$part = array_merge($temp,$part);
//			}
		}else{
			$part = M('area')->where(array('pid'=>$provience_id))->select();
		}
		$this->assign('part',$part);
		$this -> display();
	}

	/**
	 * 创建显示案例方法
	 */
	public function case_show() {
		$case_id = I("get.id");
		if(IS_POST){
			$data = I('post.');
			$re   = $this->bespoke->store($data);
			if(!$re){
				echo '<script type="text/javascript">alert("申请失败");location.href = "'.U('case_show').'"</script>';
			}else{
				echo '<script type="text/javascript">alert("申请成功，工作人员会在12小时内与您取得联系，请保持手机畅通；");location.href = "'.U('case_show',array('id'=>$case_id)).'"</script>';
			}
		}
		
		$data['case'] = M('merchant_case') -> where(array('merchant_case_id' => $case_id)) -> find();
		$data['case']['img_url'] = explode("|", $data['case']['img_url']);
		$data['merchant'] = M('merchant') -> field('merchant_id,is_apply,is_del,email,password,leagal_person', TRUE) -> where(array('merchant_id' => $data['case']['merchant_id'])) -> find();
		$data['merchant']['area'] = explode('|', $data['merchant']['area']);
		foreach ($data['merchant']['area'] as $k => &$v) {
			$v = M('area') -> where(array('area_id' => $v)) -> getField('aname');
		}
		$data['cases'] = M('merchant_case') -> where(array('merchant_id' => $data['case']['merchant_id'])) -> select();
		foreach ($data['cases'] as $k => &$v) {
			$v['img_url'] = explode("|", $v['img_url']);
		}
		$data['member'] = M('member')->where(array('member_id'=>$_SESSION['member_id']))->find();
		$this -> assign('data', $data);
		$this -> display();
	}

	/**
	 * 创建显示套餐方法
	 */
	public function combo_show() {
		$combo_id = I("get.combo_id");
		if(IS_POST){
			$data = I('post.');
			$re   = $this->bespoke->store($data);
			if(!$re){
				echo '<script type="text/javascript">alert("申请失败");location.href = "'.U('combo_show').'"</script>';
			}else{
				echo '<script type="text/javascript">alert("申请成功，工作人员会在12小时内与您取得联系，请保持手机畅通；");location.href = "'.U('combo_show',array('combo_id'=>$combo_id)).'"</script>';
			}
		}
		
		$data['combo'] = M('merchant_combo') -> where(array('merchant_combo_id' => $combo_id)) -> find();
		$data['combo']['img_url'] = explode("|", $data['combo']['img_url']);
		$data['merchant'] = M('merchant') -> field('merchant_id,is_apply,is_del,email,password,leagal_person', TRUE) -> where(array('merchant_id' => $data['combo']['merchant_id'])) -> find();
		$data['merchant']['area'] = explode('|', $data['merchant']['area']);
		foreach ($data['merchant']['area'] as $k => &$v) {
			$v = M('area') -> where(array('area_id' => $v)) -> getField('aname');
		}
		$data['combos'] = M('merchant_combo') -> where(array('merchant_id' => $data['combo']['merchant_id'])) -> select();
		foreach ($data['combos'] as $k => &$v) {
			$v['img_url'] = explode("|", $v['img_url']);
		}
		$data['member'] = M('member')->where(array('member_id'=>$_SESSION['member_id']))->find();
		$this -> assign('data', $data);
		$this -> display();
	}

	/**
	 * 创建提问申请方法
	 */
	public function apply_question() {
		$data = I('post.');
		$data['member_name'] = M('member') -> where(array('member_id' => $_SESSION['member_id'])) -> getField('member_name');
		$data['addtime'] = time();
		$re = $this -> merchant_discuss -> store($data);
		if (!$re) {
			$error = $this -> merchant_discuss -> getError();
			$this -> ajaxReturn(array('type' => 0, 'content' => $error));
			exit ;
		} else {
			$this -> ajaxReturn(array('type' => 1, 'content' => '提问成功'));
			exit ;
		}
	}

	/**
	 * 创建预约申请方法
	 */
	public function bespoke_replay() {
		$data = I('post.');
		$merchant_id = $data['merchant_id'];
		unset($data['merchant_id']);
		$data['b_name'] = '商家个人主页';
		$re = $this->bespoke->store($data);
		if(!$re){
			echo '<script type="text/javascript">alert("预约失败");location.href = "'.U('home/merchant/index').'"</script>';
		}else{
			echo '<script type="text/javascript">alert("预约成功，工作人员会在12小时内与您取得联系，请保持手机畅通；");location.href = "'.U('home/merchant/index',array('merchant_id'=>$merchant_id)).'"</script>';
		}
	}

	/**
	 * 创建会员点评方法
	 */
	public function remark_on(){
		$data['merchant_id'] = I('get.merchant_id');
		$data['merchant_remark_type'] = M("merchant_remark_type")->select();
		foreach ($data['merchant_remark_type'] as $key => $value) {
			$data['type_select'] = explode("|", $value['type_select']);
		}
		$data['member'] = M('member')->where(array('member_id'=>$_SESSION['member_id']))->find();
//		dump($data);die;
		$this->assign('data',$data);
		$this->display();
	}

	/**
	 * 创建添加点评方法
	 */
	public function chenck_remark_on(){
		$data = I('post.');
		$arr  = array(
			'member_name' =>$data['member_name'],
			'content'     =>$data['content'],
			'addtime'     =>time(),
			'list_img'    =>implode('|', $data['list_img']),
			'space_num'   =>$data['space_num'],
			'service_type'=>$data['service_type'],
			'star_num'    =>$data['star_num'],
			'merchant_id' =>$data['merchant_id']
		);
		$re = M('merchant_remark')->add($arr);
		if(!$re){
			echo '<script type="text/javascript">alert("评论失败");</script>';
		}else{
			echo '<script type="text/javascript">alert("评论成功");location.href = "'.U('index',array('merchant_id'=>$arr['merchant_id'])).'"</script>';
		}
	}


}
