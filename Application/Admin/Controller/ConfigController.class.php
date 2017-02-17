<?php namespace Admin\Controller;
header('Content-Type: text/html; charset=utf-8');
use Think\Controller;
use Common\Model\ConfigModel;

/**
 * 创建后台首页控制器
 * 16/08/03
 * boom
 */
class ConfigController extends CommonController {
	protected $config;
	
	public function __construct(){
		parent::__construct();
		$this->config = new ConfigModel;
	}
	
	public function index(){
		$data['config'] = $this->config->getData();
		$this->assign('data',$data);
		$this->display();
	}
	public function addArticle(){
		$pageData = M('Site_config_type') -> select();
		$this -> assign('pageData',$pageData);
		$this->display();
	}
	public function edit(){
		if(IS_POST){
			$data = I('post.');
			if(strstr($data['attr_value'][0],'system')){
				echo 2;die;
				$arr = array(
					'attr_value' => implode("|", $data['url']),
					'attr_name'  => $data['attr_name'],
					'site_config_type_id' => $data['site_config_type_id'],
				);
			}else{
				$arr = array(
					'attr_value' => $data['attr_value'],
					'attr_name'  => $data['attr_name'],
					'site_config_type_id' => $data['site_config_type_id'],
				);
			}
			$re = M("site_config")->where(array('site_config_id'=>$data['site_config_id']))->save($arr);
			if($re){
				redirect(U('index'));
			}else{
				echo '<script type="text/javascript">alert("编辑失败，请重新编辑");</script>';
			}
		}
		
		$site_config_id = I('get.site_config_id');
		$data = M('site_config')->where(array('site_config_id'=>$site_config_id))->find();
		if(strstr($data['attr_value'], 'system')){
			$data['url'] = explode("|", $data['attr_value']);
		}else{
		}
		$this->assign('data',$data);
		$pageData = M('Site_config_type')->select();
		$this->assign('pageData',$pageData);
		$this->display();
	}
	/**
	 * 创建添加站点配置的方法
	 */
	public function check(){
		$data = I('post.');
		$re   = $this->config->addData($data);
		if(!$re){
			echo '<script type="text/javascript">alert("添加失败");history.back(0);</script>';
		}else{
			echo '<script type="text/javascript">alert("添加成功");location.href="'.U('index').'";</script>';
	 	}
	}
	/**
	  * 创建删除的方法
	  */
	  public function del_config(){
	  	if(IS_AJAX){
	  		$data = I('post.');
			$arr = array(
				'site_config_id'=>$data['del']
			);
			$re = $this->config->delData($arr);
	  	}
	  }
	
}