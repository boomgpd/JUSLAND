<?php namespace Admin\Controller;
header('Content-Type: text/html; charset=utf-8');
use Think\Controller;
use Common\Model\PhotorecomModel;

/**
 * 创建首页推荐的控制器
 * 16/08/03
 * boom
 */
class PhotorecomController extends CommonController {
	protected $recom;
	
	public function __construct(){
		parent::__construct();
		$this->recom = new PhotorecomModel;
	}
	/**
	 * 创建推荐显示的方法
	 */
	public function index(){
		$data = M('photo_recom_type')->where(array('is_del'=>0,'p_id'=>0))->select();
		foreach ($data as $key => &$value) {
			$value['cid'] = M('photo_category')->field('p_c_id,cname')->where(array('p_c_id'=>$value['cid'],'is_del'=>0))->find();
		}
		$this->assign('data',$data);
		$this->display();
	}
	public function index_two(){
		$get = I('get.id');
		$data = M('photo_recom_type')->where(array('is_del'=>0,'p_id'=>$get))->select();
		foreach ($data as $key => &$value) {
			$value['cid'] = M('photo_category')->field('p_c_id,cname')->where(array('p_c_id'=>$value['cid'],'is_del'=>0))->find();
		}
		$this->assign('data',$data);
		$this->display();
	}
	public function index_three(){
		$get = I('get.id');
		$res = M('photo_category')->field('p_c_id')->where(array('is_del'=>0,'p_id'=>$get))->select();
		foreach ($res as $key => $value) {
			$re = M('photo_recom_type')->where(array('is_del'=>0,'p_id'=>$get))->getField('pro_id',TRUE);
			if($re) $where['p_p_id'] = array('not in',implode(",", $re));
			$where['is_del'] = 0;$where['p_c_id'] = $value['p_c_id']; 
			$data = M('photo_product')->where($where)->select();
			foreach ($data as $k => &$v) {
				$v['addtime'] = date('Y-m-d H:i:s',$v['addtime']);
				$v['p_c_id'] = M('photo_category')->field('p_id,p_c_id,cname')->where(array('is_del'=>0,'p_c_id'=>$v['p_c_id']))->find();
			}
		}
		$temp  =  '';
		foreach ($res as $key => $value) {
			$re = M('photo_recom_type')->where(array('is_del'=>0,'p_id'=>$get))->getField('pro_id',TRUE);
			if($re) $where['p_p_id'] = array('in',implode(",", $re));
			$where['is_del'] = 0;$where['p_c_id'] = $value['p_c_id']; 
			$temp = M('photo_product')->where($where)->select();
			foreach ($temp as $k => &$v) {
				$v['addtime'] = date('Y-m-d H:i:s',$v['addtime']);
				$v['p_c_id'] = M('photo_category')->field('p_id,p_c_id,cname')->where(array('is_del'=>0,'p_c_id'=>$v['p_c_id']))->find();
			}
		}
		$this->assign('data',$data);
		$this->assign('temp',$temp);
		$this->display();
	}
	/**
	 * 创建爱你添加推荐商品的方法
	 */
	public function add_pro(){
		if(IS_POST){
			$data = I('post.');
			$re = $this->recom->add($data);
			if($re){
				alert('添加成功',U('index_three',array('id'=>$_GET['p_id'])));
			}else{
				alert($this->recom->getError(),U('add_pro',array('id'=>$_GET['cid'],'pro_id'=>$_GET['pro_id'],'p_id'=>$_GET['p_id'])));
			}
		}
		$this->display();
	}
	/**
	 * 编辑推荐的商品
	 */
	 public function edit_pro(){
	 	$get = I('get.');
		if(IS_POST){
			$post = I('post.');
			if($post['is_poric'] == 1){
	    		$info = M('photo_recom_type')->where(array('is_del'=>0,'cid'=>$post['p_id']))->getField('p_id');
	    		$cid = M("photo_recom_type")->where(array('is_del'=>0,'p_id'=>$info))->getField('cid',TRUE);
	    		foreach ($cid as $key => $value) {
	    			$bool[] = M('photo_recom_type')->where(array('pid'=>$value,'is_del'=>0,'is_poric'=>1))->find();
	    		}
	    		if($bool){
	    			alert('已有左侧重点推荐，只可以推荐一个');
	    			return FALSE;
	    		}
	    	}
			$re = M('photo_recom_type')->where(array('pro_id'=>$get['pro_id']))->save($post);
			if($re){
				alert('编辑成功',U('index_three',array('id'=>$get['id'])));
			}else{
				alert('编辑失败');
			}
		}
		$data = M('photo_recom_type')->where(array('pro_id'=>$get['pro_id']))->find();
		$this->assign('data',$data);
	 	$this->display();
	 }
	 /**
	  * 下线功能
	  */
	public function dele_pro(){
		$get  = I('get.pro');
		$re = M('photo_recom_type')->where(array('pro_id'=>$get))->delete();
		if($re){
			alert('下线成功');
		}else{
			alert('下线失败');
		}
	}
	 
	/**
	 * 创建添加类型的方法
	 */
	public function add(){
		$get = I('get.');
		if(IS_POST){
			$data = I('post.');
			$re = $this->recom->add($data);
			if($re){
				if($get){
					alert('添加成功',U('index_two',array('id'=>$get['id'])));
				}else{
					alert('添加成功',U('index'));
				}
			}else{
				if($get){
					alert($this->recom->getError(),U('add',array('id'=>$get['id'])));
				}else{
					alert($this->recom->getError(),U('add'));
				}
			}
		}
		if($get['id']){
			$re = M('photo_recom_type')->where(array('is_del'=>0,'p_id'=>0))->getField('cid',TRUE);
			if($re) $where['p_c_id'] = array('not in',implode(",", $re));
			$where['is_del'] = 0;$where['level'] = 1;$where['p_id'] = 0;
			$data = M('photo_category')->field('p_c_id,cname')->where($where)->select();
		}else{
			$re = M('photo_recom_type')->where(array('is_del'=>0,'p_id'=>$get['id']))->getField('cid',TRUE);
			if($re) $where['p_c_id'] = array('not in',implode(",", $re));
			$where['is_del'] = 0;$where['p_id'] = $get['id'];
			$data = M('photo_category')->field('p_c_id,cname')->where($where)->select();
		}
		$this->assign('data',$data);
		$this->display();
	}
	/**
	 * 创建删除的方法
	 */
	public function dele(){
		if(IS_AJAX){
			$data = I('post.del');
			$re = M('photo_recom_type')->where(array('p_r_t_id'=>$data))->save(array('is_del'=>1));
			$res = M('photo_recom')->where(array('p_r_t_id'=>$data))->save(array('is_del'=>1));
			$this->ajaxReturn($re);exit;
		}
	}
	/**
	 * 创建编辑的方法
	 */
	public function save(){
		if(IS_AJAX){
			$data = I('post.');
			$re = M('photo_recom_type')->where(array('p_r_t_id'=>$data['id']))->save(array('p_r_t_name'=>$data['p_r_t_name']));
			$this->ajaxReturn($re);exit;
		}
	} 
	/**
	 * 创建推荐显示页面的方法
	 */
	public function index_list(){
		$get = I('get.id');
		$data = M('photo_recom')->where(array('is_del'=>0,'p_r_t_id'=>$get))->select();
		$data['type_name'] = M('photo_recom_type')->where(array('is_del'=>0,'p_r_t_id'=>$get))->getField('p_r_t_name');
		$this->assign('data',$data);
		$this->display();
	}
	/**
	 * 创建添加推荐的方法
	 */
	public function add_list(){
		if(IS_AJAX){
			$data = I('post.');
			$data['type'] = M('photo_recom_type')->where(array('is_del'=>0))->select();
		}
		$data['list'] = M('photo_category')->where(array('is_del'=>0,'p_id'=>0,'level'=>1))->select();
		$this->assign('data',$data);
		$this->display();
	}
	/**
	 * 创建限时首页推荐
	 */
	public function index_class(){
		$re = M('photo_odds')->where(array('is_del'=>0,'is_page'=>1))->select();
		foreach ($re as $key => $value) {
			$temp      = M('photo_product')->where(array('p_p_id'=>$value['p_p_id'],'is_del'=>0))->find();
			$temp['tnt'] = $value['tnt'];
			$data[] = $temp;
		}
		$this->assign('data',$data);
		$this->display();
	}
	/**
	 * 推荐商品页面方法
	 */
	public function add_class(){
		$re = M('photo_odds')->where(array('is_del'=>0,'is_page'=>0))->select();
		foreach ($re as $key => $value) {
			$temp      = M('photo_product')->where(array('p_p_id'=>$value['p_p_id'],'is_del'=>0))->find();
			$temp['tnt'] = $value['tnt'];
			$data[] = $temp;
		}
		$this->assign('data',$data);
		$this->display();
	}
	/**
	 * 信息获取
	 */
	public function ajax_alss(){
		if(!IS_AJAX) return FALSE;
		$data = I('post.id');
		$re = M('photo_odds')->where(array('p_p_id'=>$data))->find();
		$this->ajaxReturn($re);exit;
	}
	/**
	 * 首推方法
	 */
	public function edit_class(){
		$get = I('post.');
		$info = M('photo_odds')->where(array('is_del'=>0,'is_page'=>1))->count();
		if($info < 5){
			$re = M('photo_odds')->where(array('p_p_id'=>$get['p_p_id']))->save(array('is_page'=>1,'desc'=>$get['desc'],'url'=>$get['url']));
			if($re){
				alert('推荐成功',U('index_class'));die;
			}else{
				alert('推荐失败',U('add_class'));
			}
		}else{
			alert('已经够了，只可以推荐四个',U('add_class'));
		}
	}
	/**
	 * 下线方法
	 */
	public function del_class(){
		$get = I('get.p_p_id');
		$re = M('photo_odds')->where(array('p_p_id'=>$get))->save(array('is_page'=>0));
		if($re){
			alert('下线成功',U('index_class'));die;
		}else{
			alert('下线失败',U('index_class'));die;
		}
	}
	/**
	 * 畅销推荐
	 */
	public function index_sell(){
		$data = M('photo_sell')->where(array('is_del'=>0))->select();
		foreach ($data as $key => &$value) {
			$value['addtime'] = date('Y-m-d',$value['addtime']);
			$value['p_p_id'] = M('photo_product')->where(array('is_del'=>0,'p_p_id'=>$value['p_p_id']))->getField('p_name');
		}
		$this->assign('data',$data);
		$this->display();
	}
	public function add_sell(){
		if(IS_POST){
			$data = I('post.');
			$data['addtime'] = time();
			$re = M('photo_sell')->add($data);
			if($re){
			alert('畅推成功',U('index_sell'));die;
			}else{
				alert('畅推失败',U('add_sell'));die;
			}
		}
		$re = M('photo_sell')->where(array('is_del'=>0))->getField('p_p_id',TRUE);
		if($re != null){
			$where['p_c_id'] = array('not in',implode(',', $re));
		} 
		$where['is_del'] = 0;
		$data = M('photo_product')->where($where)->select();
		foreach ($data as $key => &$value) {
			$value['picture'] = explode(',', $value['picture']);
		}
		$this->assign('data',$data);
		$this->display();
	}
	/**
	 * 编辑畅销
	 */
	public function edit_sell(){
		if(IS_AJAX){
			$data = I('post.');
			$re = M('photo_sell')->where(array('sell_id'=>$data['id']))->save(array('sell_title'=>$data['sell_title'],'sell_desc'=>$data['sell_desc']));
			if($re){
				$this->ajaxReturn($re);exit;
			}else{
				$this->ajaxReturn(FALSE);exit;
			}
		}
	}
	/**
	 * 下线畅销
	 */
	public function dele_sell(){
		if(IS_AJAX){
			$data = I('post.del');
			$re = M('photo_sell')->where(array('sell_id'=>$data))->save(array('is_del'=>1));
			if($re){
				$this->ajaxReturn($re);exit;
			}else{
				$this->ajaxReturn(FALSE);exit;
			}
		}
	}
}