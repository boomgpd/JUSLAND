<?php namespace Admin\Controller;
header('Content-Type: text/html; charset=utf-8');
use Think\Controller;

/**
 * 创建后台首页控制器
 * 16/08/03
 * boom
 */
class IndexController extends CommonController {
	
	private function thumb($image,$info){
		if(!is_file(C('__THUMB__').$info)) return FALSE;
	 	$image->open(C('__THUMB__').$info);
	 	$width = $image->width();
    	$height = $image->height();
    	$w = 410/$width;
    	$h = round($height*$w);
		
    	$name = 'Picture_thumb/';

    	$dir = C('__THUMB__').$name;
    	is_dir($dir) || mkdir($dir,0777);
    	$date = substr($info,8,10).'/';
    	$dir .= $date;
    	is_dir($dir) || mkdir($dir,0777);
    	$image->thumb(410,$h)->save($dir.substr($info,19));
    	return $name.$date.substr($info,19);
	 }


    public function index(){
//  	$data = M('Picture')->select();
//  	$image = new \Think\Image();
//  	foreach($data as $k => $v){
//  		$data[$k]['url'] = explode('|',$v['url']);
//  	};
//  	foreach($data as $k => $v){
//  		$thumb = array();
//  		foreach($v['url'] as $a => $b){
//  			$img = $this->thumb($image,$b);
//  			$thumb[] = $img;
//  		};
//
//  		$arr['thumb'] = implode('|',$thumb);
//  		M('Picture')->where(array('p_id'=>$v['p_id']))->save($arr);
//  	};die;
    	$auth = $_SESSION[C('ADMIN_AUTH_KEY')];
    	$this -> assign('auth',$auth);
    	$urls = M('Project')->where(array('is_del'=>0))->select();
    	$this->assign('urls',$urls);
		$this->display();
    }
	
	public function welcome(){
		$this->display();
    }
	
	//跳转方法
	public function jump($id=NULL){
		if(empty($id)){
			alert('非法请求');die;
		};
		if($_SESSION[C('ADMIN_AUTH_KEY')] === true){
			$data = M('Project')->where(array('is_del'=>0,'pid'=>$id))->find();
			if(is_null($data)){
				alert('数据异常');die;
			};
			$url = $data['url'];
			$arr['admin_name'] = $_SESSION['admin_name'];
			$arr['key'] = $data['key'];
			post($url,$arr);die;
		}else{
			$data = M('Project_admin')->where(array('is_del'=>0,'pid'=>$id,'aid'=>$_SESSION['admin_id']))->find();
			if(is_null($data)){
				alert('非法请求');die;
			};
			$url = $data['url'];
			$arr['admin_name'] = $_SESSION['admin_name'];
			$arr['key'] = $data['key'];
			post($url,$arr);die;
		};
	}

	//验证权限
	// public function verify(){
	// 	if(!IS_POST){
	// 		alert('非法请求');die;
	// 	};
	// 	$post = I('post.');
	// 	if(!isset($post['key']) || !isset($post['admin_name'])){
	// 		alert('非法请求');die;
	// 	}else{
	// 		if(!$post['key'] != C('KEY')){
	// 			alert('非法请求');die;
	// 		}else{
	// 			$_SESSION['admin'] = $post;
	// 			alert('欢迎您,'.$post['admin_name'],U('Index/index'));die;
	// 		};
	// 	};
	// }
	
	
	
}