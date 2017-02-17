<?php namespace Home\Controller;
use Think\Controller;
use Common\Model\AreaModel;
use Common\Model\BespokeModel;
use Common\Model\BananerModel;
use Home\Model\PictureModel;
use Home\Model\MemberModel;
use Common\Model\MenuModel;
use Common\Model\ConfigModel;
use Common\Model\FriendModel;
use Home\Model\MediaModel;
/**
 * 前台首页控制器
 * boom
 * 08/11
 */
class IndexController extends CommonController {
	protected $area;
	protected $bespoke;
	protected $member;
	protected $menu;
	protected $bananer;
	protected $config;
	protected $friend;
	protected $picture;
	protected $adver;
	protected $media;
	public function __construct (){
		parent::__construct();
		$this-> member   	= new MemberModel;
		$this-> bespoke  	= new BespokeModel;
		$this-> area    	= new AreaModel;
		$this-> menu     	= new MenuModel;
		$this-> bananer     = new BananerModel;
		$this-> config     	= new ConfigModel;
		$this-> friend     	= new FriendModel;
		$this-> picture     = new PictureModel;
		$this-> media       = new MediaModel;
		$this->adver        = D('Adver');
	}
	/**
	 *创建首页控制器
	 */
    public function index(){
    	$data['member']     = $this->member->getData();
		$data['area']       = $this->area->getData();
		$data['menu']       = $this->menu->getData();
		$data['ban_give']   = $this->bananer->bananer_show(1);
		$data['ban_advert'] = $this->bananer->bananer_show(3);
		$data['config']     = $this->config->getData();
		$data['friend']     = $this->friend->getData();
		$data['que']        = explode("\\", $data['config'][12]['attr_value']);
		$data['list_show']  = $this->picture->list_show();
		$adver_type_id      = M('adver_type')->where(array('adver_type_name'=>'首页通栏广告'))->getField('adver_type_id');
    	$data['adver']      = $this->adver->getData($adver_type_id);
    	$data['media']      = $this->media->indexData();
    	$this->assign('data',$data);
    	$this->display();
    }
	
	/**
	 * 创建首页策划、报价、找公司、立即报价申请的方法
	 */
	 public function insp_plot(){
	  	$data = I('post.');
		if($data['b_name']=='首页标价' && IS_AJAX){
			$re   = $this->bespoke->store_plot($data);
			$condition = array();
			$condition['area_id'] = array('eq',implode("|",$data['area_id']));
			$condition['is_del'] = array('eq',0);
			$data = M('index_area_prica')->field('addtime,area_id,index_area_prica_id,is_del',TRUE)->where($condition)->find();
			$data['zong'] = array_sum($data);
			foreach($data as $k=>$v){
				$data[$k] = sprintf("%.2f", $v/10000);
			}
			if($data && $re){
				$this->ajaxReturn($data);exit;
			}
		}
		if(IS_POST){
			$re   = $this->bespoke->store_plot($data);
			if(!$re){
				echo '<script type="text/javascript">alert("申请失败");location.href = "'.U('home/index/index').'"</script>';
			}else{
				echo '<script type="text/javascript">alert("申请成功，工作人员会在12小时内与您取得联系，请保持手机畅通；");location.href = "'.U('home/index/index').'"</script>';
			}
		}
	  }
}