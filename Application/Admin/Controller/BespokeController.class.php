<?php namespace Admin\Controller;
header('Content-Type: text/html; charset=utf-8');
use Think\Controller;
use Common\Model\BespokeModel;
use Common\Model\Diy_marrierModel;
use Common\Model\MerchantModel;

/**
 * 创建后台首页控制器
 * 16/08/03
 * boom
 */
class BespokeController extends CommonController {
	protected $bespoke;
	protected $diy_marrier;
	protected $merchant;
	
	public function __construct(){
		parent::__construct();
		$this->bespoke = new BespokeModel;
		$this->diy_marrier = new Diy_marrierModel;
		$this->merchant = new MerchantModel;
		
	}
	
	/**
	 * 创建显示预约方法
	 */
    public function index(){
		$getData = I('get.');
		$data = $this->bespoke->getData(array('is_deal'=>$getData['is_deal'],'bespoke_type'=>$getData['bespoke_type']));
		$this->assign('data',$data);
		$bespoke_type = M('bespoke_type')->select();
		$this->assign('bespoke_type',$bespoke_type);
		$is_deal = array(
			array('is_deal'=>'1','deal'=>'未接单'),
			array('is_deal'=>'2','deal'=>'处理中'),
			array('is_deal'=>'3','deal'=>'考虑中'),
			array('is_deal'=>'4','deal'=>'已成功'),
			array('is_deal'=>'5','deal'=>'未成功'),
		);
		$this->assign('is_deal',$is_deal);
//		dump($is_deal);die;
		$this->display();
    }
	
	/**
	 * 创建抢单方法
	 */
	public function rob_bespoke(){
		if(IS_POST){
			$data = I('post.');
			$data['deal_time'] = time();
			$re = $this->bespoke->where(array('bespoke_id'=>$data['bespoke_id']))->save($data);
			if($re || $re === 0){
				redirect(U('rob_bespoke',array('id'=>$data['bespoke_id'])));
			}			
		}
		
		$bespoke_id = I('get.id');
		$data = current($this->bespoke->getData(array('bespoke_id'=>$bespoke_id)));
		if($data['is_deal'] == 1){
			$this->bespoke->where(array('bespoke_id'=>$bespoke_id,))->save(array('is_deal'=>2,'admin_id'=>$_SESSION['admin_id'],'rob_time'=>time()));
		}
		$data = current($this->bespoke->getData(array('bespoke_id'=>$bespoke_id)));
		$this->assign('data',$data);
		$this->display();
	}
	 
	/**
	 * 创建过往用户
	 */
	 public function self_center(){
	 	$data = $this->bespoke->getData(array('admin_id'=>$_SESSION['admin_id']));
		$this->assign('data',$data);
		$this->display();
	 }
	
	
	/**
	 * 创建异步方法判定是否有新的预约
	 */
	 public function step(){
	 	
	 	$re = I('post.data');
	 	$data = array();
	 	//查询商家、预约、婚礼人是否有未处理消息
	 	$data['data'][] = array(//预约
	 		'num' => intval($this->bespoke->where(array('is_del'=>0,'is_deal'=>1))->count()),
	 		'msg' => '条预约信息未处理',
	 		'url' => U('admin/bespoke/index'),
	 	);
	 	$data['data'][] = array(//婚庆
	 		'num' => intval($this->merchant->where(array('is_del'=>0,'is_apply'=>0))->count()),
	 		'msg' => '条婚礼商家信息待审核',
	 		'url' => U('admin/merchant/apply'),
	 	);
	 	$data['data'][] = array(//影楼
	 		'num' => intval(M('Photo')->where(array('is_del'=>0,'apply_type'=>0))->count()),
	 		'msg' => '条影楼商家信息待审核',
	 		'url' => U('admin/photo/apply'),
	 	);
	 	$data['data'][] = array(//酒店
	 		'num' => intval(M('Hotel')->where(array('is_del'=>0,'apply_type'=>0))->count()),
	 		'msg' => '条酒店商家信息待审核',
	 		'url' => U('admin/hotel/apply'),
	 	);
	 	$data['data'][] = array(//婚礼人
	 		'num' => intval($this->diy_marrier->where(array('is_del'=>0,'is_apply'=>0))->count()),
	 		'msg' => '条婚礼人信息待审核',
	 		'url' => U('admin/marrier/apply'),
	 	);
	 	$data['data'][] = array(//影楼预约
	 		'num' => intval(M('photo_make')->where(array('is_page'=>0))->count()),
	 		'msg' => '条影楼预约信息待处理',
	 		'url' => U('admin/photo/make'),
	 	);
	 	$data['data'][] = array(//酒店预约
	 		'num' => intval(M('hotel_make')->where(array('is_page'=>0))->count()),
	 		'msg' => '条酒店预约信息待处理',
	 		'url' => U('admin/hotel/make'),
	 	);
	 	$type = 0;
	 	//检测是否有新的
	 	foreach($data['data'] as $k => $v){
	 		if($v['num'] > 0){
	 			$type = 1;
	 		};
	 	};
	 	$data['type'] = $type;
	 	$this->ajaxReturn($data);die;
	 }
	
}