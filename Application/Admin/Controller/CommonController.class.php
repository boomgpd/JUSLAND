<?php namespace Admin\Controller;
use Think\Controller;
use Org\Util\Rbac;

class CommonController extends Controller {
	public function __construct(){
		parent::__construct();
		//没有登录
    	if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
            $this->redirect('Admin/Login/login');
       };
       $notAuth = in_array(MODULE_NAME, explode(',', C('NOT_AUTH_MODULE'))) || in_array(ACTION_NAME, C('NOT_AUTH_ACTION'));
       	//权限验证
	    // if(C('USER_AUTH_ON') && !$notAuth) {
	    // 	$str = '<script type="text/javascript">alert("你没有对应的权限");parent.location.href= "'.U('Admin/index/index').'"</script>';
	    //     RBAC::AccessDecision() || die($str);
	    // };
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
	    	if($name == 'Picture'){
	    		$data = $this->thumb($name,$info);
	    	}else if($name == 'shop_detail_img'){
	    		$data = $this->shop_thumb($name,$info,540,540,6);
	    	}else if($name == 'shop_spec'){
	    		$data = $this->shop_thumb($name,$info,150,150,6);
	    	}else{
	    		$data = $info['Filedata']['savepath'].$info['Filedata']['savename'];
	    	};
	        //上传成功，把上传好的信息返给js
	        echo json_encode($data);exit;
	    }
	 }
	  /**
	   * 谭超的瀑布流缩略
	   */
	  private function thumb($name,$info){
	 	$image = new \Think\Image();
	 	$image->open(C('__THUMB__').$info['Filedata']['savepath'].$info['Filedata']['savename']);
	 	$width = $image->width();
    	$height = $image->height();
    	$w = 410/$width;
    	$h = round($height*$w);
    	$name .= '_thumb/';
    	$dir = C('__THUMB__').$name;
    	is_dir($dir) || mkdir($dir,0777);
    	$date = date('Y-m-d').'/';
    	$dir .= $date;
    	is_dir($dir) || mkdir($dir,0777);
    	$image->thumb(410,$h)->save($dir.$info['Filedata']['savename']);
    	$arr[] = $info['Filedata']['savepath'].$info['Filedata']['savename'];
    	$arr[] = $name.$date.$info['Filedata']['savename'];
    	return $arr;
	 }

	/**
	  * 谭超的商城缩略方法
	  */
	  private function shop_thumb($name,$info,$w,$h,$type){
	 	$image = new \Think\Image();
	 	$image->open(C('__THUMB__').$info['Filedata']['savepath'].$info['Filedata']['savename']);
	 	$width = $image->width();
		$height = $image->height();
		$name .= '_thumb/';
		$dir = C('__THUMB__').$name;
		is_dir($dir) || mkdir($dir,0777);
		$date = date('Y-m-d').'/';
		$dir .= $date;
		is_dir($dir) || mkdir($dir,0777);
		$image->thumb($w,$h,$type)->save($dir.$info['Filedata']['savename']);
		$arr[] = $info['Filedata']['savepath'].$info['Filedata']['savename'];
		$arr[] = $name.$date.$info['Filedata']['savename'];
		return $arr;
	 }
	  
	
}