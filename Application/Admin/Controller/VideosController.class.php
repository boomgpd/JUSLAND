<?php namespace Admin\Controller;
header('Content-Type: text/html; charset=utf-8');
use Think\Controller;
use Admin\Model\VideosModel;
/**
 * 创建后台视频控制器
 * 16/09/11
 * geyu
 */
class VideosController extends CommonController {
	/**
	 * 创建成员属性
	 */
	 protected $videos;
	
	/**
	 * 创建构造函数
	 */
	 public function __construct(){
	 	parent::__construct();
		$this->videos = new VideosModel;
	 }
	 /**
	  * 创建后台显示的方法
	  */
	public function index(){
		$get  = I('get.');
		$data = $this->videos->getData($get);
		$arr = array();
		foreach ($data as $key => $value) {
			$att = array();
			$att['box'] = $value;
			$att['box']['type_name'] = M('new_video_type')->where(array('is_del'=>0,'new_video_type_id'=>$value['new_video_type_id']))->getField('type_name');
			$arr[] = $att;
		}
		$this->assign('arr',$arr);
		$type = $this->videos->type();
		$this->assign('type',$type);
		$this->assign('session',$_SESSION);
		$this->display();
	}
	/**
	 * 创建添加视频的方法
	 */
	public function add_videos(){
		if(IS_POST){
			$data = I('post.');
			$arr = array(
				'video_title'      =>$data['video_title'],
				'admin_id'         =>$data['admin_id'],
				'video_des'        =>$data['video_des'],
				'video_url'        =>$data['video_url'],
				'video_width_img'  =>implode("|", $data['video_width_img']) ,
				'video_height_img' =>implode("|", $data['video_height_img']) ,
				'list_img'         =>implode("|", $data['list_img']) ,
				'new_video_type_id'=>$data['new_video_type_id'],
				'addtime'          =>time(),
				'is_bananer'       =>1,
				'is_hot'           =>1,
				'is_del'           =>0
			);
			$re = $this->videos->addData($arr);
			if($re){
				redirect(U('index'));
			}else{
				echo '<script type="text/javascript">alert("添加失败，请重新添加");</script>';
			}
		}
		$data = $this->videos->type();
//		dump($data);die;
		$this->assign('data',$data);
		$this->display();
	}
	/**
	  * 创建视频的删除
	  */
	  public function del_videos(){
	  	if(IS_AJAX){
	  		$data = I('post.');
			$arr = array(
				'new_video_id'=>$data['del']
			);
			$re = $this->videos->delData($arr);
	  	}
	  }
	/**
	 * 创建视频编辑的方法
	 */
	public function save_videos(){
		if(IS_POST){
			$data = I('post.');
			$arr = array(
				'video_title'     =>$data['video_title'],
				'new_video_id'    =>$data['new_video_id'],
				'video_des'       =>$data['video_des'],
				'video_url'       =>$data['video_url'],
				'admin_id'        =>$data['admin_id'],
				'video_width_img' =>$data['video_width_img'],
				'list_img'        =>$data['list_img'],
				'video_height_img'=>$data['video_height_img']
			);
			$data = $this->videos->saveData($arr);
			if($data){
				redirect(U('index'));
			}else{
				echo '<script type="text/javascript">alert("编辑失败，请重新编辑");</script>';
			}
		}
		if(IS_GET){
			$data = I('get.');
			$data = $this->videos->sava_show($data);
		}
//		dump($data);die;
		$this->assign('data',$data);
		$this->assign('session',$_SESSION);
		$this->display();
	}
	/**
	 * 创建是否轮播/热点推荐的方法
	 */
	public function getsave(){
		if(IS_POST){
			$data = I('post.');
			if($data['is']!=null){
				if($data['is']==1){
					$bool = M('new_video')->where(array('is_bananer'=>2,))->getField('new_video_id',TRUE);
					$info = array_merge($bool,$data['li']);
					foreach ($data['li'] as $key => $value) {
						if(in_array($value,$bool)){
							echo '<script type="text/javascript">alert("已勾选过了");location.href="'.U('index').'";</script>';
						}else{
							if(count($info)<=3 && count($bool)<=3 && count($data['li'])<=3){//轮播
								foreach ($data['li'] as $key => $value) {
									$re = M('new_video')->where(array('new_video_id'=>$value))->save(array('is_bananer'=>2));
								}
								if($re){
									redirect(U('index'));
								}else{
									echo '<script type="text/javascript">alert("编辑失败，请重新编辑");location.href="'.U('index').'";</script>';
								}
							}else{
								echo '<script type="text/javascript">alert("请先取消后勾选，轮播只可以同时选取三个");location.href="'.U('index').'";</script>';
							}
						}
					}
				}else if($data['is']==2){//热点推荐
					$bool = M('new_video')->where(array('is_hot'=>2))->getField('new_video_id',TRUE);
					$info = array_merge($bool,$data['li']);
					foreach ($data['li'] as $key => $value) {
						if(in_array($value,$bool)){
							echo '<script type="text/javascript">alert("已勾选过了");location.href="'.U('index').'";</script>';
						}else{
							if(count($info)<=6 && count($bool)<=6 && count($data['li'])<=6){
								foreach ($data['li'] as $key => $value) {
									$re = M('new_video')->where(array('new_video_id'=>$value))->save(array('is_hot'=>2));
								}
								if($re){
									redirect(U('index'));
								}else{
									echo '<script type="text/javascript">alert("编辑失败，请重新编辑");location.href="'.U('index').'";</script>';
								}
							}else{
								echo '<script type="text/javascript">alert("请先取消后勾选，热点推荐只可以同时选取六个");location.href="'.U('index').'";</script>';
							}
						}
					}
				}else if($data['is']==3){//取消轮播
						foreach ($data['li'] as $key => $value) {
							$re = M('new_video')->where(array('new_video_id'=>$value))->save(array('is_bananer'=>1));
						}
						if($re){
							redirect(U('index'));
						}else{
							echo '<script type="text/javascript">alert("编辑失败，请重新编辑");location.href="'.U('index').'";</script>';
						}
				}else if($data['is']==4){//取消热点推荐
						foreach ($data['li'] as $key => $value) {
							$re = M('new_video')->where(array('new_video_id'=>$value))->save(array('is_hot'=>1));
						}
						if($re){
							redirect(U('index'));
						}else{
							echo '<script type="text/javascript">alert("编辑失败，请重新编辑");location.href="'.U('index').'";</script>';
						}
				}
			}else{
				echo '<script type="text/javascript">alert("请选择类型呀！");location.href="'.U('index').'";</script>';
			}
		}
	}
	
	
	
	
	/**
	 * 创建显示分类的方法
	 */
	public function index_type(){
		$data['type'] = $this->videos->type();
//		dump($data);die;
		$this->assign('data',$data);
		$this->display();
	}
	/**
	 * 创建添加视频分类的方法
	 */
	public function add_type(){
		if($_POST){
			$data = I('post.');
			$re = $this->videos->addData($data);
			if($re){
				redirect(U('index_type'));
			}else{
				echo '<script type="text/javascript">alert("编辑失败，请重新编辑");</script>';
			}
		}
		$this->display();
	}
	/**
	 * 创建分类的编辑
	 */
	public function  save_type(){
		if(IS_AJAX){
	 		$data = I('post.');
			$arr = array(
				"new_video_type_id"=>$data['tid'],
				'type_name'        =>$data['cont'],
				'type_des'         =>$data['link_'],
				'is_del'           =>0
			);
//			dump($arr);die;
			$re = $this->videos->saveData($arr);
	 	}
	}
	/**
	  * 创建分类的删除
	  */
	  public function del_type(){
	  	if(IS_AJAX){
	  		$data = I('post.');
			$arr = array(
				'new_video_type_id'=>$data['del']
			);
			$re = $this->videos->delData($arr);
	  	}
	  }
}