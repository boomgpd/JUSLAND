<?php
namespace Admin\Controller;
use Common\Model\ShopSpecModel;
header("Content-type: text/html; charset=utf-8");
//商城规格控制器
class ShopSpecController extends CommonController {
    private $spec;
	public function __construct(){
		parent::__construct();
        $this->spec = new ShopSpecModel;
	}
	/**
	 * 规格列表方法
	 */
	public function index(){
		$data = $this->spec->where(array('is_del'=>0))->select();
		$this->assign('data',$data);
		$this->display();
	}
	/**
	 * 添加规格方法
	 */
	public function add(){
		if(IS_POST){
			$post = I('post.');
			$data = $this->spec->addData($post);
			if($data){
				alert('添加成功',U('index'));die;
			}else{
				alert($this->spec->getError());die;
			}
		}
		$this->display();
	}
	/**
	 * 编辑规格名称
	 */
	public function edit(){
		if(IS_AJAX){
			$ajax = I('post.');
			$re = $this->spec->where(array('s_id'=>$ajax['id']))->save(array('spec_name'=>$ajax['name']));
			$this->ajaxReturn($re);exit;
		}
	}
	/**
	 * 删除 规格方法
	 */
	public function dele(){
		if(IS_AJAX){
			$ajax = I('post.id');
			$re  = $this->spec->where(array('s_id'=>$ajax['id']))->save(array('is_del'=>1));
			$this->ajaxReturn($re);exit;
		}
	}
}