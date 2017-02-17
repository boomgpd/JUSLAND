<?php namespace Hotel\Controller;
use Think\Controller;
/**
 * 影楼管理公共控制器
 * boom
 * 08/12
 */
class CommonController extends Controller {
	
	public function __construct(){
		parent::__construct();
		if(!$_SESSION['hotel_id']){
			redirect(U('home/index/index'));
		}
	}
	

	/**
	 * 创建上传图片类
	 */
	 public function upload(){
	 	$name = I('get.name');
	 	$upload = new \Think\Upload();// 实例化上传类
	    $upload->maxSize   =     3145728 ;// 设置附件上传大小
	    $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
	    $upload->rootPath  =     __ROOT__; // 设置附件上传根目录
	    $upload->savePath  =     $name.'/'; // 设置附件上传（子）目录
	    // 上传文件 
	    $info   =   $upload->upload();
	    if (empty($info)) {
	        echo json_encode($this->error());exit;
	    } else {
	    	//上传成功，把上传好的信息返给js
	        $data = $info['Filedata']['savepath'].$info['Filedata']['savename'];
	        echo json_encode($data);exit;
	    }
	 }

	 /**
	 * 创建获取对应省份的市区
	 */
	public function step_area(){
		if(!IS_AJAX) return FALSE;
		$pid = I('post.pid');
		if($pid == 0){
			$this->ajaxReturn(FALSE);exit;
		}
		
		$data = M('area')->where(array('pid'=>$pid))->select();
		if($data){
			$this->ajaxReturn($data);exit;
		}else{
			$this->ajaxReturn(FALSE);exit;
		}
		
	}
}