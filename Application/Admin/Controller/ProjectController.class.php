<?php
namespace Admin\Controller;
use Admin\Model\ProjectModel;
use Admin\Model\AdminModel;

//用户控制器
class ProjectController extends CommonController {
	
	private $project;
    private $admin;
	
	public function __construct(){
		parent::__construct();
        $this->project = new ProjectModel;
        $this->admin = new AdminModel;
		
	}
	
	//项目首页
    public function p_index(){
    	$data = $this->project->where(array('is_del'=>0))->select();
        $this->assign('data',$data);
        $this->display();
    }

    //项目添加
    public function p_add(){
        if(IS_POST){
            $post = I('post.');
            $re = $this->project->store($post);
            if(!$re){
                alert($this->project->getError());die;
            };
            alert('添加成功',U('p_index'));die;
        };
        $this->display();
    }

    //项目编辑
    public function p_edit($id=NULL){
        if(empty($id)){
            alert('非法请求');die;
        };
        if(IS_POST){
            $post = I('post.');
            $re = $this->project->edit($post);
            if(!$re){
                alert($this->project->getError());die;
            };
            alert('编辑成功',U('p_index'));die;
        };
        $data = $this->project->where(array('is_del'=>0,'pid'=>$id))->find();
        $this->assign('data',$data);
        $this->display();
    }

    //项目删除
    public function p_del($id=NULL){
        if(empty($id)){
            alert('非法请求');die;
        };
        $this->project->where(array('is_del'=>0,'pid'=>$id))->save(array('is_del'=>1));
        alert('删除成功',U('p_index'));die;
    }

    //用户列表
    public function a_index(){
        $data = $this->admin->where(array('is_del'=>0))->select();
        foreach($data as $k => $v){
            $pids = M('Project_admin')->where(array('is_del'=>0,'aid'=>$v['admin_id']))->getField('pid',true);
            $pids = implode(',',$pids);
            $title = $this->project->where(array('pid'=>array('IN',$pids)))->getField('title',true);
            $data[$k]['title'] = implode(',',$title);
        };
        $this->assign('data',$data);
        $this->display();
    }
    
    //添加权限
    public function a_edit($id=NULL){
        if(empty($id)){
            alert('非法请求');die;
        };
        if(IS_POST){
            $post = I('post.');
            $re = $this->project->auth($post);
            if(!$re){
                alert($this->project->getError());die;
            };
            alert('编辑成功',U('a_index'));die;
        };
        $pids = M('Project_admin')->where(array('is_del'=>0,'aid'=>$id))->getField('pid',true);
        if(is_null($pids)){
            $pids = array();
        };
        $p_data = $this->project->where(array('is_del'=>0))->select();
        $this->assign('p_data',$p_data);
        $this->assign('pids',$pids);
        $this->assign('aid',$id);
        $this->display();
    }

}