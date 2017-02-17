<?php namespace Merchant\Controller;
use Think\Controller;
use Common\Model\Mer_sonModel;

/**
 * 影楼子账号管理控制器
 * 谭超
 * 16/11/5
 */
Class SonController extends CommonController {

	private $son;
	
	public function __construct(){
		$this->son = new Mer_sonModel;
		parent::__construct();

	}

	//子账号列表
	public function son_list(){
		$data = $this->son->where(array('type'=>'merchant','pid'=>$_SESSION['merchant_id'],'is_del'=>0))->select();
		$this->assign('data',$data);
		$this->display();
	}

	//添加子账号
	public function son_add(){
		if(IS_POST){
			$post = I('post.');
			$re = $this->son->store($post,'merchant');
			if(!$re){
				alert($this->son->getError());die;
			};
			alert('添加成功',U('Son/son_list'));die;
		};
		$this->display();
	}

	//角色列表
	public function role_list(){
		$data = $this->merr->where(array('module_name'=>MODULE_NAME,'pid'=>$_SESSION['photo_id'],'is_del'=>0))->select();
		$this->display();
	}

	//添加角色
	public function role_add(){
		if(IS_POST){
			$post = I('post.');
			$re = $this->merr->store($post);
			if(!$re){
				alert($this->merr->getError());die;
			};
			alert('添加成功',U('role_list'));die;
		};
		//获得控制器数据
		$id = M('Mer_module')->where(array('name'=>MODULE_NAME,'is_del'=>0))->getField('id');
		$con_data = M('Mer_controller')->where(array('module_id'=>$id,'is_del'=>0))->select();
		$this->assign('con_data',$con_data);
		$this->display();
	}

	//异步查询方法
	public function get_action(){
		if(!IS_AJAX) return FALSE;
		$id = I('post.id');
		$m_id = M('Mer_controller')->where(array('id'=>$id,'is_del'=>0))->getField('module_id');
		$m_name = M('Mer_module')->where(array('id'=>$m_id,'is_del'=>0))->getField('name');
		if($m_name != MODULE_NAME) return FALSE;
		$data = M('Mer_action')->where(array('controller_id'=>$id,'is_del'=>0))->select();
		$this->ajaxReturn($data);die;
	}
	

}