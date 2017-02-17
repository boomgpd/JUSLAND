<?php namespace Admin\Controller;
header('Content-Type: text/html; charset=utf-8');
use Think\Controller;
use Common\Model\AreaModel;
use Common\Model\HotelModel;


/**
 * 创建后台商家控制器
 * 16/08/10
 * boom
 */
class HotelController extends CommonController {
	protected $area;
	protected $hotel;
	
	/**
	 * 创建构造函数
	 */
	 public function __construct(){
	 	parent::__construct();
		$this->area = new AreaModel;
		$this->hotel = new HotelModel;
	 }
	
	/**
	 * 创建酒店显示列表
	 */
	public function index(){
		$data = $this->hotel->where(array('is_del'=>0))->select();
		$this->assign('data',$data);
		$this->display();
	}
	
	
	 /**
	  * 创建商家审核方法
	  */
	  public function apply(){
	  	/**
		 * 1.获取审核未通过的商家信息
		 * 2.通过循环将地区信息转化为字符内容
		 * 3.分配数据
		 */
	  	$data = $this->hotel->where(array('is_del'=>0,'apply_type'=>0))->select();
		foreach($data as $k=>$v){
			$area[] = $v['provience'];
			$area[] = $v['city'];
			foreach($area as $kk=>$vv){
				$value[] = M('area')->where(array('area_id'=>$vv))->getField('aname');
			};
			$data[$k]['area']= implode(',',$value);
		};
		$this->assign('data',$data);
		$this->display();
	  }

	  /**
	  * 创建异步审核方法
	  */
	  public function audit(){
	  	/**
		 * 1.判定请求方式
		 * 2.获取传输数据
		 * 3.调用模型，更新字段
		 */
	  	if(!IS_AJAX) return FALSE;
		$data = I('post.');
		$re = $this->hotel->audit($data);
		if(!$re){
			$this->ajaxReturn(array('type'=>'0','content'=>'审核失败'));exit;
		}else{
			$this->ajaxReturn(array('type'=>'1','content'=>'审核成功'));exit;
		}
	}
	  /**
	   * 创建酒店属性的方法
	   */
	public function tad(){
		$data = M('hotel_name')->where(array('is_del'=>0))->select();
		foreach ($data as $key => &$value) {
			$value['addtime'] = date('Y-m-d',$value['addtime']);
		}
		$this->assign('data',$data);
		$this->display();
	}
	/**
	* 推荐到列表页
	*/
	public function list_is(){
		if(!IS_GET) return FALSE;
		$data = I('get.id');
		$re = M('hotel_name')->where(array('is_list'=>1))->count();
		if($re < 4){
			$re = M('hotel_name')->where(array('p_p_a_id'=>$data))->save(array('is_list'=>1));
			if($re){
				alert('显示成功',U('tad'));
			}else{
				alert('显示失败',U('tad'));
			}
		}else{
			alert('只可以显示4个规格',U('tad'));
		}
	}
	/**
	* 取消推荐到列表页
	*/
	public function list_over(){
		if(!IS_GET) return FALSE;
		$data = I('get.id');
		$re = M('hotel_name')->where(array('p_p_a_id'=>$data))->save(array('is_list'=>0));
		if($re){
			alert('取消成功',U('tad'));
		}else{
			alert('取消失败',U('tad'));
		}
	}
	/**
	 * 创建添加酒店属性的方法
	 */
	public function add(){
		if(IS_POST){
			$data = I('post.');
			$datas = explode('|', $data['p_a_name']);
			foreach ($datas as $key => &$value) {
				$arr['p_a_name'] = $value;
				$arr['addtime'] = time();
				$arr['level'] = $data['level'];
				$re = M('hotel_name')->add($arr);
			}
			if($re){
				alert('添加成功',U('tad'));
			}else{
				alert('添加失败',U('add'));
			}
		}
		$this->display();
	}
	/**
	 * 编辑的方法
	 */
	public function edit(){
		if(IS_AJAX){
			$data = I('post.');
			$re = M('hotel_name')->where(array('p_p_a_id'=>$data['id']))->save(array('p_a_name'=>$data['name']));
			if($re){
				$this->ajaxReturn($re);exit;
			}else{
				$this->ajaxReturn(FALSE);exit;
			}
		}
	}
	/**
	 * 删除的方法
	 */
	public function dele(){
		if(IS_AJAX){
			$data = I('post.');
			$re = M('hotel_name')->where(array('p_p_a_id'=>$data['id']))->save(array('is_del'=>1));
			if($re){
				$this->ajaxReturn($re);exit;
			}else{
				$this->ajaxReturn(FALSE);exit;
			}
		}
	}
	/**
	 * 创建预约信息显示的方法
	 */
	public function make(){
		$data = M('hotel_make')->select();
		foreach ($data as $key => &$value) {
			$value['addtime'] = date('Y-m-d',$value['addtime']);
		}
		$this->assign('data',$data);
		$this->display();
	}
	/**
	 * 创建处理预约信息的方法
	 */
	public function check(){
		$get = I('get.id');
		$re = M('hotel_make')->where(array('make_id'=>$get))->save(array('is_page'=>1));
		if($re){
			alert('处理成功',U('make'));
		}
	}
	
	
}