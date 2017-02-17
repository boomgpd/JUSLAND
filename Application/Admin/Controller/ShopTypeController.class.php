<?php
namespace Admin\Controller;
use Common\Model\Shop_typeModel;
use Common\Model\Shop_type_attrModel;

//商城分类控制器
class ShopTypeController extends CommonController {
	
	private $type; 
	private $type_attr; 
	
	public function __construct(){
		$this->type = new Shop_typeModel;
		$this->type_attr = new Shop_type_attrModel;
		parent::__construct();
	}

    //首页
    public function index(){
        $condition = array('is_del'=>0);
        if(IS_POST){
            $name = I('post.name');
            if(!empty($name)){
                $condition['tname'] = array('LIKE','%'.$name.'%');
            };
        };
    	$data = $this->type->where($condition)->select();
    	$this->assign('data',$data);
        $this->display();
    }

    //添加
    public function add(){
    	if(IS_POST){
    		$post = I('post.');
    		$re = $this->type->store($post);
    		if(!$re){
    			alert($this->type->getError());die;
    		};
    		alert('添加成功',U('index'));die;
    	};
    	$this->display();
    }

    //编辑
    public function edit($id=NULL){
    	if(empty($id)) return FALSE;
    	if(IS_POST){
    		$post = I('post.');
    		$re = $this->type->edit($post);
    		if(!$re){
    			alert($this->type->getError());die;
    		};
    		alert('编辑成功',U('index'));die;
    	};
    	$data = $this->type->where(array('tid'=>$id,'is_del'=>0))->find();
    	$this->assign('data',$data);
    	$this->display();
    }

    //类型属性列表
    public function attr_index($id=NULL){
    	if(empty($id)) return FALSE;
    	$data = $this->type_attr->where(array('type_id'=>$id,'is_del'=>0))->select();
    	$this->assign('data',$data);
    	$this->assign('tid',$id);
    	$this->display();
    }

    //添加类型属性
    public function attr_add($id=NULL){
    	if(empty($id)) return FALSE;
    	if(IS_POST){
    		$post = I('post.');
    		$post['type_id'] = $id;
    		$re = $this->type_attr->store($post);
    		if(!$re){
    			alert($this->type_attr->getError());die;
    		};
    		alert('添加成功',U('attr_index',array('id'=>$id)));
    	}
    	$this->display();
    }

    //编辑类型属性
    public function attr_edit($id=NULL){
    	if(empty($id)) return FALSE;
    	if(IS_POST){
    		$post = I('post.');
    		$re = $this->type_attr->edit($post);
    		if(!$re){
    			alert($this->type_attr->getError());die;
    		};
    		alert('编辑成功',U('attr_index',array('id'=>$re['type_id'])));die;
    	};
    	$data = $this->type_attr->where(array('taid'=>$id,'is_del'=>0))->find();
    	$this->assign('data',$data);
    	$this->display();
    }

    //删除类型属性
    public function attr_del($id=NULL){
    	if(empty($id)) return FALSE;
    	$re = $this->type_attr->where(array('taid'=>$id,'is_del'=>0))->find();
    	if(is_null($re)){
    		alert('删除成功');die;
    	}else{
    		$this->type_attr->where(array('taid'=>$id))->save(array('is_del'=>1));
    		alert('删除成功',U('attr_index',array('id'=>$re['type_id'])));die;
    	};
    }
	
}