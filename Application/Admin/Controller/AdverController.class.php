<?php namespace Admin\Controller;
header('Content-Type: text/html; charset=utf-8');
use Think\Controller;
use Common\Model\Adver_typeModel;
use Common\Model\AdverModel;

/**
 * 创建广告管理后台控制器
 * boom
 * 2016/08/28
 */
class AdverController extends CommonController{
	protected $adver_type;
	protected $adver;
	
	
	public function __construct(){
		parent::__construct();
		$this->adver_type = new Adver_typeModel;
		$this->adver = new AdverModel;
	}
	
	/**
	 * 创建广告类型管理方法
	 */
	 public function adver_type(){
	 	$data = $this->adver_type->getData();
		$this->assign('data',$data);
		$this->display();
	 }
	
	/**
	 * 创建添加广告分类方法
	 */
	public function add_adver_type(){
		if(IS_POST){
			$data = explode("|",I('post.adver_type_name'));
//			dump($data);die;
			foreach($data as $k=>$v){
				$arr = array('adver_type_name'=>$v);
				$re = $this->adver_type->store($arr);
				if(!$re){
					echo '<script type="text/javascript">alert("添加失败");location.href = "'.U('admin/adver/add_adver_type').'"</script>';
				}
			}
			
			echo '<script type="text/javascript">alert("添加成功");location.href = "'.U('admin/adver/adver_type').'"</script>';
			
		}
		
		$this->display();
	}
	/**
	 * 创建删除分类方法
	 */
	public function adver_type_del(){
		$id = I('get.adver_type_id');
		$re = $this->adver_type->save(array('adver_type_id'=>$id,'is_del'=>1));
		if(!$re){
			echo '<script type="text/javascript">alert("删除失败");location.href = "'.U('admin/adver/adver_type').'"</script>';
		}else{
			echo '<script type="text/javascript">alert("删除成功");location.href = "'.U('admin/adver/adver_type').'"</script>';
		}
	}
	
	/**
	 * 创建保存广告分类方法
	 */
	 public function adver_type_save(){
	 	$data = I('post.');
		foreach($data['adver_type_id'] as $k=>$v){
			$arr = array();
			$arr['adver_type_id'] = $v;
			$arr['adver_type_name'] = $data['adver_type_name'][$k];
			$re = $this->adver_type->edit($arr);
			if(!$re || $re == 'FALSE'){
				echo '<script type="text/javascript">alert("保存失败");location.href = "'.U('admin/adver/adver_type').'"</script>';
			}echo '<script type="text/javascript">alert("保存成功");location.href = "'.U('admin/adver/adver_type').'"</script>';
		
	 }
		}
		
	
	/**
	 * 创建显示广告方法
	 */
	 public function adver(){
	 	$adver_type_id = I('get.type_id');
		$data = $this->adver->getData($adver_type_id);
		$this->assign('data',$data);
		$adver_type = $this->adver_type->getData();
		$this->assign('adver_type',$adver_type);		
		$this->display();
	 }
	
	/**
	 * 创建编辑的方法
	 */
	public function edit(){
		if(IS_POST){
			$data = I('post.');
			$re = $this->adver->chenk_edit($data);
			if(!$re || $re == 'FALSE'){
				echo '<script type="text/javascript">alert("保存失败");location.href = "'.U('admin/adver/adver').'"</script>';
			}else{
				echo '<script type="text/javascript">alert("保存成功");location.href = "'.U('admin/adver/adver').'"</script>';
			}
		}
		$avder_id = I('get.adver_id');
		$data = $this->adver->editData($avder_id);
//		dump($data);die;
		$this->assign('data',$data);
		$this->display();
	
	}
		
	
	/**
	 * 创建广告添加方法
	 */
	 public function add_adver(){
	 	if(IS_POST){
	 		$data = I('post.');
			$data['pic_src'] = implode("|", $data['pic_src']);
			$re = $this->adver->store($data);
			if(!$re){
				echo '<script type="text/javascript">alert("添加失败");</script>';
			}else{
				echo '<script type="text/javascript">alert("添加成功");location.href = "'.U('admin/adver/adver').'"</script>';
			}
	 	}
		
		$adver_type = $this->adver_type->getData();
		$this->assign('adver_type',$adver_type);
		$this->display();
	 }
	
	/**
	 * 创建编辑广告方法
	 */
	 public function save_adver(){
	 	$data = I('post.');
		foreach($data['adver_id'] as $k=>&$v){
			$arr = array(
				'adver_id'	=>	$data['adver_id'][$k],
				'adver_name'	=>	$data['adver_name'][$k],
				'adver_url'	=>	$data['adver_url'][$k],
				'adver_type_id'	=>	$data['adver_type_id'][$k]
			);
			$re = $this->adver->edit($arr);
			if(!$re || $re == 'FALSE'){
				echo '<script type="text/javascript">alert("保存失败，请重新保存");location.href = "'.U('admin/adver/adver').'"</script>';
			}
		}
		
		echo '<script type="text/javascript">alert("保存成功");location.href = "'.U('admin/adver/adver').'"</script>';
		
	 } 
	/**
	 * 创建删除的方法
	 */
	public function del(){
		if(IS_AJAX){
			$data = I('post.del');
			$re = M('adver')->where(array('adver_id'=>$data))->save(array('is_del'=>1));
			$this->ajaxReturn($re);
		}
	}
}




































