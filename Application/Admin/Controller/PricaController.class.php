<?php
namespace Admin\Controller;
use Admin\Model\PricaModel;

/**
 * 创建首页预算报价控制器 && 详情页的免费报价
 * 2016.10.20
 * 葛羽
 */
class PricaController extends CommonController {
	protected $prica;
	public function __construct(){
	 	parent::__construct();
		$this->prica = new PricaModel;
	 }
	/**
	 * 创建显示的方法
	 */
	public function index(){
		$data = $this->prica->getData();
		$this->assign('data',$data);
		$this->display();
	}
	/**
	 * 创建添加的方法
	 */
	public function add(){
		if(IS_AJAX){
			$data = I('post.area_id');
			$re = $this->prica->area_city($data);
			$this->ajaxReturn($re);exit;
		}
		if(IS_POST){
			$data = I('post.');
			$re = $this->prica->addData($data);
			if($re){
				redirect(U('index'));
			}else{
				echo '<script type="text/javascript">alert("添加失败");</script>';
			}
		}
		$data = $this->prica->area_city();
		$this->assign('data',$data);
		$this->display();
	}
	/**
	 * 创建产出的方法
	 */
	public function delc(){
		if(IS_AJAX){
			$data = I('post.del');
			$re = M('index_area_prica')->where(array('index_area_prica_id'=>$data))->save(array('is_del'=>1));
			$this->ajaxReturn($re);exit;
		}
	}
	/**
	 * 创建编辑页面的方法
	 */
	public function edit(){
		if(IS_POST){
			$data = I('post.');
			$re = $this->prica->editData($data);
			if($re){
				redirect(U('index'));
			}else{
				echo '<script type="text/javascript">alert("添加失败");</script>';
			}
		}
		if(IS_AJAX){
			$data = I('post.area_id');
			$re = $this->prica->area_city($data);
			$this->ajaxReturn($re);exit;
		}
		$data = $this->prica->area_city();
		$this->assign('data',$data);
		$get = I('get.index_area_prica_id');
		$arr = $this->prica->getData($get);
		$this->assign('arr',$arr);
		$this->display();
	}
	
	
	/**
	 * 下面是详情页的免费报价
	 * 创建显示页面的方法
	 */
	public function index_list(){
		$data = $this->prica->getData_list();
		$this->assign('data',$data);
		$this->display();
	}
	/**
	 * 创建添加的方法
	 */
	public function add_list(){
		if(IS_POST){
			$data = I('post.');
			$data['area_id'] =implode('|', $data['area_id']);
			$data['addtime'] = time();
			$re = $this->prica->add_list($data);
			if($re){
				redirect(U('index_list'));
			}else{
				echo '<script type="text/javascript">alert("添加失败");</script>';
			}
		}
		if(IS_AJAX){
			$data = I('post.area_id');
			$re = $this->prica->area_city($data);
			$this->ajaxReturn($re);exit;
		}
		$data = $this->prica->area_city();
		$this->assign('data',$data);
		$this->display();
	}
	 /**
	 * 创建产出的方法
	 */
	public function delc_list(){
		if(IS_AJAX){
			$data = I('post.del');
			$re = M('detail_area_prica')->where(array('detail_area_prica_id'=>$data))->save(array('is_del'=>1));
			$this->ajaxReturn($re);exit;
		}
	}
	/**
	 * 创建编辑页面的方法
	 */
	public function edit_list(){
		if(IS_POST){
			$data = I('post.');
			$data['area_id'] =implode('|', $data['area_id']);
			$re = $this->prica->editData_list($data);
			if($re){
				redirect(U('index_list'));
			}else{
				echo '<script type="text/javascript">alert("添加失败");</script>';
			}
		}
		if(IS_AJAX){
			$data = I('post.area_id');
			$re = $this->prica->area_city_list($data);
			$this->ajaxReturn($re);exit;
		}
		$data = $this->prica->area_city();
		$this->assign('data',$data);
		$get = I('get.detail_area_prica_id');
		$arr = $this->prica->getData_list($get);
		$this->assign('arr',$arr);
		$this->display();
	}
	/**
	 * 下面是报价规格的所有方法
	 */
	public function count_type(){
		$data = M('count_type_prica')->where(array('is_del'=>0))->select();
		foreach ($data as $key => &$value) {
			$value['addtime'] = date('Y-m-d',$value['addtime']);
		}
		$this->assign('data',$data);
		$this->display();
	}
	public function add_type(){
		if(IS_POST){
			$data = I('post.');
			$data['addtime'] = time();
			$re = M('count_type_prica')->add($data);
			if($re){
				redirect(U('count_type'));
			}else{
				echo '<script type="text/javascript">alert("添加失败");</script>';
			}
		}
		$this->display();
	}
	public function delc_type(){
		if(IS_AJAX){
			$data = I('post.del');
			$re = M('count_type_prica')->where(array('count_type_prica_id'=>$data))->save(array('is_del'=>1));
			$this->ajaxReturn($re);exit;
		}
	}
	public function edit_type(){
		if(IS_POST){
			$data = I('post.');
			$re = M('count_type_prica')->where(array('count_type_prica_id'=>$data['count_type_prica_id']))->save($data);
			if($re){
				redirect(U('count_type'));
			}else{
				echo '<script type="text/javascript">alert("添加失败");</script>';
			}
		}
		$data = I('get.');
		$re = M('count_type_prica')->where(array('count_type_prica_id'=>$data['count_type_prica_id'],'is_del'=>0))->find();
		$this->assign('re',$re);
		$this->display();
	}
}
