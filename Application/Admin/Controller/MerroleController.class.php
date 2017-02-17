<?php
namespace Admin\Controller;
use Admin\Model\MerrModel;

//商家角色控制器
class MerroleController extends CommonController {
	
	private $merr;
	
	public function __construct(){
		$this->merr = new MerrModel;
		parent::__construct();
	}
	
	//首页
    public function index(){
   		$data = $this->merr->where(array('is_del'=>0))->select();
   		$this->assign('data',$data);
        $this -> display();
    }

    //添加
    public function add(){
    	if(IS_POST){
    		$post = I('post.');
    		$re = $this->merr->store($post);
    		if(!$re){
    			alert($this->merr->getError());die;
    		};
    		alert('添加成功',U('Merrole/index'));
    	};
    	//系统数据
    	$data = M('Mer_node')->where(array('level'=>1,'is_del'=>0))->select();
    	$this->assign('data',$data);
    	$this->display();
    }

    //编辑
    public function edit($id=NULL){
    	if(empty($id)) return FALSE;
    	if(IS_POST){
    		$post = I('post.');
    		$re = $this->merr->edit($post);
    		if(!$re){
    			alert($this->merr->getError());die;
    		};
    		alert('编辑成功',U('Index/index'));die;
    	};
    	$data = $this->merr->where(array('id'=>$id,'is_del'=>0))->find();
    	$this->assign('data',$data);
    	$this->display();
    }

    //查看节点属性
    public function look_list($id=NULL,$level=1,$pid=0){
        if(empty($id)) return FALSE;
    	$data = $this->merr->get_data($id,$level,FALSE,$pid);
    	$this->assign('data',$data);
    	$this->assign('level',$level);
    	$this->assign('id',$id);
        $this->assign('pid',$pid);
    	$this->display();
    }

    //添加
    public function add_node($id=NULL,$pid=NULL,$level=NULL){
    	if(empty($id)) return FALSE;
    	if(IS_POST){
    		$post = I('post.');
    		$re = M('Mer_role_node')->where(array('role_id'=>$id,'node_id'=>$post['node_id'],'is_del'=>0))->find();
    		if(!is_null($re)){
    			alert('已有相同的');die;
    		};
    		$post['role_id'] = $id;
    		M('Mer_role_node')->add($post);
    		alert('添加成功',U('look_list',array('id'=>$id,'level'=>$level)));die;
    	};
    	$data = $this->merr->get_data($id,$level,true,$pid);
    	$this->assign('data',$data);
    	$this->assign('level',$level);
    	$this->display();
    }

    //异步获得数据
    public function get_data(){
    	if(!IS_AJAX) return FALSE;
    	$post = I('post.');
    	$re = M('Mer_node')->where(array('id'=>$post['val'],'level'=>$post['level'],'is_del'=>0))->find();
    	if(is_null($re)) return FALSE;
    	$data = M('Mer_node')->where(array('pid'=>$re['id'],'level'=>$post['level']+1,'is_del'=>0))->select();
    	if(is_null($data)) return FALSE;
    	$this->ajaxReturn($data);die;
    }
    
}