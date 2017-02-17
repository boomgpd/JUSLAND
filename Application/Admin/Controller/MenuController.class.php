<?php namespace Admin\Controller;
header('Content-Type: text/html; charset=utf-8');
use Think\Controller;
use Common\Model\MenuModel;

/**
 * 创建后台首页控制器
 * 16/08/03
 * geyu
 */
class MenuController extends CommonController {
	protected $menu;
	
	public function __construct(){
		parent::__construct();
		$this->menu = new MenuModel;
	}
	/**
	 * 创建添加方法
	 */
	public function add_article(){
		if(IS_POST){
			$data = I('post.');
			$re   = $this->menu->addMenu($data);
			if(!$re){
				echo '<script type="text/javascript">alert("添加失败");</script>';
			}else{
				echo '<script type="text/javascript">alert("添加成功");</script>';
			}
		} 
		
		$this->display();
	}
	/**
	 * 创建显示的方法
	 */
	public function menu(){
		$data['menu'] = $this->menu->getData();
		$this->assign('data',$data);
		$this->display();
	}
	/**
	 * 创建编辑的方法
	 */
	 public function save_menu(){
	 	if(IS_AJAX){
	 		
	 		$data = I('post.');
			$arr = array(
				"m_id"=>$data['tid'],
				'menu_cont'=>$data['cont'],
				'menu_link'=>$data['link_']
			);
			$re = $this->menu->saveData($arr);
			$this->ajaxReturn($re);exit;
	 	}
	 }
	 /**
	  * 创建删除的方法
	  */
	  public function del_menu(){
	  	if(IS_AJAX){
	  		$data = I('post.');
			$arr = array(
				'm_id'=>$data['del']
			);
			$re = $this->menu->delData($arr);
			$this->ajaxReturn($re);exit;
	  	}
	  }
}