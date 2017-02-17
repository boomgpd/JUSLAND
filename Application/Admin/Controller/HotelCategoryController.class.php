<?php
namespace Admin\Controller;
use Think\Controller;
use Common\Model\Hotel_categoryModel;

/**
 * 创建后台酒店分类控制器
 * 16/11/12
 * 葛羽
 */
class HotelCategoryController extends CommonController {
	protected $table;
	
	public function __construct(){
		parent::__construct();
		$this->table = new Hotel_categoryModel;
	}
	
	
	/**
	 * 影楼分类显示方法
	 */
    public function index(){
    	$data = $this->table->where(array('is_del'=>0,'level'=>1))->select();
		foreach($data as $k=>$v){
			$data[$k]['addtime'] = date("Y-m-d H:i:s",$v['addtime']);
		}
		$this->assign('data',$data);
		$this->display();
    }
	
	/**
	 * 影楼分类添加方法
	 */
	 public function add(){
	 	if(IS_POST){
	 		$data = I('post.');	
//			dump($data);die;
			$re = $this->table->add($data);
			if(!$re){
				alert($this->table->getError(),U('add'));
			}else{
				if($data['level'] == '1'){
					alert('添加成功',U('index'));
				}else if($data['level'] == '2'){
					alert('添加成功',U('index_two_list',array('id'=>$data['p_id'])));
				}
			}
	 	}
		$this->display();
	 }
	 
	/**
	 * 创建编辑方法
	 */
	 public function edit(){
	 	$id = I('get.');
	 	if(IS_POST){
	 		$data = I('post.');
			$data['p_c_id'] = $id['id'];
			$re = $this->table->edit($data);
			if(!$re && $re === FALSE){
				alert($this->table->getError(),U('index'));
			}else{
				if($id['level'] == '1'){
					alert('编辑成功',U('index'));
				}else if($id['level'] == '2'){
					alert('编辑成功',U('index_two_list',array('id'=>$id['p_id'])));
				}
			}
	 	}
		
		$data = $this->table->where(array('p_c_id'=>$id['id'],'is_del'=>0))->find();
		if($data){
			$this->assign('data',$data);
			$this->display();
		}else{
			alert('该分类已经删除或未添加，请重新操作',U('index'));
		}
	 }
	 /**
	  * 创建删除方法
	  */
	  public function del(){
	  	$id = I('get.');
		if($id['level'] == 1){
			$re = $this->table->where(array('p_c_id'=>$id['id']))->save(array('is_del'=>1));
			$re1 = $this->table->where(array('p_id'=>$id['id']))->getField('p_c_id',TRUE);
			foreach ($re1 as $key => $value) {
				$re3 = $this->table->where(array('p_c_id'=>$value))->save(array('is_del'=>1));
			}
		}else if($id['level'] == 2){
			$re = $this->table->where(array('p_c_id'=>$id['id']))->save(array('is_del'=>1));
		}
		if($re){
			if($id['level'] == '1'){
				alert('删除成功',U('index'));
			}else if($id['level'] == '2'){
				alert('删除成功',U('index_two_list',array('id'=>$id['id'])));
			}
		}else{
			alert('删除失败',U('index'));
		}
	  }
	  /**
	   * 创建二级分类的方法
	   */
	  	public function index_two_list(){
	  		$data = $this->table->where(array('is_del'=>0,'p_id'=>$_GET['id'],'level'=>2))->select();
			foreach($data as $k=>$v){
				$data[$k]['addtime'] = date("Y-m-d H:i:s",$v['addtime']);
			}
			$this->assign('data',$data);
	  		$this->display();
	  	}
		/**
	   * 创建三级分类的属性值的方法
	   */
	  	public function index_type_list(){
	  		if(IS_POST){
		 		$data = I('post.');
				$data['p_c_id'] = $_GET['id'];
				$re = $this->table->edit($data);
				if(!$re && $re === FALSE){
					alert($this->table->getError(),U('index'));
				}else{
					alert('编辑成功',U('index_two_list',array('id'=>$_GET['p_id'])));
				}
	 		}
	  		$data = $this->table->where(array('is_del'=>0,'p_c_id'=>$_GET['id'],'level'=>2))->find();
			$this->assign('data',$data);
	  		$this->display();
	  	}
}
