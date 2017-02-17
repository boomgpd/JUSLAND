<?php namespace Admin\Controller;
header('Content-Type: text/html; charset=utf-8');
use Think\Controller;
use Common\Model\AreaModel;
use Common\Model\PhotoModel;
use Common\Model\Photo_categoryModel;


/**
 * 创建后台商家控制器
 * 16/08/10
 * boom
 */
class PhotoController extends CommonController {
	protected $area;
	protected $photo;
	protected $category;
	
	/**
	 * 创建构造函数
	 */
	 public function __construct(){
	 	parent::__construct();
		$this->area = new AreaModel;
		$this->photo = new PhotoModel;
		$this->category = new Photo_categoryModel;

	 }
	
	/**
	 * 商家列表
	 */
	public function index(){
		$data = M('photo')->where(array('is_del'=>0))->select();
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
	  	$data = $this->photo->where(array('is_del'=>0,'apply_type'=>0))->select();
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
		$re = $this->photo->audit($data);
		if(!$re){
			$this->ajaxReturn(array('type'=>'0','content'=>'审核失败'));exit;
		}else{
			$this->ajaxReturn(array('type'=>'1','content'=>'审核成功'));exit;
		}
	  }
	

	/**
	 * 影楼分类显示方法
	 */
    public function category_index(){
    	$data = $this->category->where(array('is_del'=>0))->select();
		foreach($data as $k=>$v){
			$data[$k]['addtime'] = date("Y-m-d H:i:s",$v['addtime']);
		}
		$this->assign('data',$data);
		$this->display();
    }
	
	/**
	 * 影楼分类添加方法
	 */
	 public function category_add(){
	 	if(IS_POST){
	 		$data = I('post.');
			$re = $this->category->add($data);
			if(!$re){
				alert($this->category->getError(),U('add'));
			}else{
				alert('添加成功',U('category_index'));
			}
	 	}
		$this->display();
	 }
	 
	/**
	 * 创建分类编辑方法
	 */
	 public function category_edit(){
	 	$id = I('get.id');
	 	if(IS_POST){
	 		$data = I('post.');
			$data['p_c_id'] = $id;
			$re = $this->category->edit($data);
			if(!$re && $re === FALSE){
				alert($this->category->getError(),U('category_index'));
			}else{
				alert('编辑成功',U('category_index'));
			}
	 	}
		
		$data = $this->category->where(array('p_c_id'=>$id,'is_del'=>0))->find();
		if($data){
			$this->assign('data',$data);
			$this->display();
		}else{
			alert('该分类已经删除或未添加，请重新操作',U('category_index'));
		}
	 }
	 
	 /**
	  * 创建分类删除方法
	  */
	  public function category_del(){
	  	$id = I('get.id');
		$re = $this->category->where(array('p_c_id'=>$id))->save(array('is_del'=>1));
		if($re){
			alert('删除成功',U('category_index'));
		}else{
			alert('删除失败',U('category_index'));
		}
	}
	/**
	 * 创建预约信息显示的方法
	 */
	public function make(){
		$data = M('photo_make')->select();
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
		$re = M('photo_make')->where(array('photo_make_id'=>$get))->save(array('is_page'=>1));
		if($re){
			alert('处理成功',U('make'));
		}
	}

}