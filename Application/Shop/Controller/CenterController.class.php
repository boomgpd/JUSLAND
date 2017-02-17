<?php
namespace Shop\Controller;
use Common\Model\Shop_usedModel;

//个人中心控制器
class CenterController extends CommonController {

	private $used;
	
	public function __construct(){
		parent::__construct();
		if(!isset($_SESSION['member_id'])){
			redirect(U('Main/Login/login'));
		};
		$this->used = new Shop_usedModel;
	}

	//查看自己的贴
	public function look_p($state=NULL,$apply=NULL){
		if($apply != null){
			$data = $this->used->where(array('member_id'=>$_SESSION['member_id'],'is_del'=>0,'is_apply'=>$apply,'status'=>0))->select();
		}else if($state != null){
			unset($_GET['apply']);
			$data = $this->used->where(array('member_id'=>$_SESSION['member_id'],'is_del'=>0,'status'=>$state))->select();
		}else{
			$data = $this->used->where(array('member_id'=>$_SESSION['member_id'],'is_del'=>0,'status'=>0))->select();
		}
		foreach ($data as $key => &$value) {
			$value['addtime'] = date('Y-m-d',$value['addtime']);
		}
		$this->assign('data',$data);
		$this->display();
	}

	//编辑贴子
	public function edit_p($id=NULL){
		if(is_null($id)) return FALSE;
		$data = $this->used->where(array('id'=>$id,'member_id'=>$_SESSION['member_id'],'is_del'=>0))->find();
		if(is_null($data)) return FALSE;
		if(IS_POST){
			$post = I('post.');
			$post['id'] = $id;
			$post['is_apply'] = 0;
			if($post['showed']){
				$post['showed'] = implode("|", $post['showed']);
			}else{
				$post['showed'] = $this->upload($_FILES['showed']);
			}
			$re = $this->used->edit($post);
			if($re === FALSE){
				alert($this->used->getError());die;
			};
			alert('编辑成功,请耐心等待审核',U('look_p'));die;	
		};
		$data['showed'] = explode('|',$data['showed']);
		$data['provience'] = M('area')->field('area_id,aname')->where(array('area_id'=>$data['provience']))->find();
		$data['city'] = M('area')->field('area_id,aname')->where(array('area_id'=>$data['city']))->find();
		$provience = M('area')->where(array('pid'=>0))->select();//获得省份数据
		$city = M('area')->where(array('pid'=>$data['provience']))->select();//获得对应城市数据
		$this->assign('provience',$provience);
		$this->assign('city',$city);
		$this->assign('data',$data);
		$this->display();
	}

	//发帖方法
	public function posted(){
		if(IS_POST){
			$post = I('post.');
			$post['showed'] = $this->upload($_FILES['showed']);
			$re = $this->used->store($post);
			if(!$re){
				alert($this->used->getError());die;
			};
			alert('发布成功,我们会尽快审核,请耐心等待',U('look_p'));die;
		};
		$provience = M('area')->where(array('pid'=>0))->select();
		$this->assign('provience',$provience);
		$this->display();
	}
	//获取地区的方法
	public function get_city(){
		if(IS_AJAX){
			$data = I('post.area_id');
			$re = M('area')->field('area_id,aname')->where(array('pid'=>$data))->select();
			$this->ajaxReturn($re);exit;
		}
	}
	//删除方法
	public function dele(){
		if(IS_AJAX){
			$data = I('post.id');
			$re = $this->used->where(array('id'=>$data,'merchint_id'=>$_SESSION['member_id']))->save(array('is_del'=>1));
			$this->ajaxReturn($re);exit;
		}
	}
	//一交易的处理方法
	public function state(){
		if(IS_AJAX){
			$data = I('post.id');
			$re = $this->used->where(array('id'=>$data,'merchint_id'=>$_SESSION['member_id']))->save(array('status'=>1));
			$this->ajaxReturn($re);exit;
		}
	}
	/*
	* 二手列表也
	*/
	public function list_shop(){
		$limit = 20;
		if($_GET['page']){
			$page = $_GET['page'];
		}else{
			$page = 1;
		}
		$data =  $this->used->where(array('is_del'=>0,'is_apply'=>1))->limit($limit)->page($page)->select();
		foreach ($data as $key => &$value) {
			$value['showed'] = explode('|', $value['showed']);
		}
		$arr = $this->used->where(array('is_del'=>0,'is_apply'=>1))->count();
		$num = ceil($arr/$limit);
		if($page == 0){
			$page_left = 0;
		}else if($page < 4){
			$page_left = $page-1;
		}else if($page > 3){
			$page_left = 3;
		}
		if($num == 1 || $num == 0){
			$page_type = FALSE;
		}else{
			$page_type = TRUE;
		}
		$page_right =  $num-$page;
		if($page_right > 3) $page_right = 3;
		$data['count'] =array('page_left'=>$page_left,'num'=>$num,'page_right'=>$page_right,'page_type'=>$page_type,'page'=>$page);
		// dump($data);die;
		$this->assign('data',$data);
		$this->display();
	}
	/*
	* 二手详情也
	*/
	public function details($id=NULL){
		if(!$id){
			return FALSE;
			alert('页面丢失');
		}
		$data = $this->used->where(array('id'=>$id))->find();
		$data['showed'] = explode("|", $data['showed']);
		$this->assign('data',$data);
		$this->display();
	}













	/**
	*图片上传
	*/
	public function upload(){
	    $upload = new \Think\Upload();// 实例化上传类
	    $upload->maxSize   =     3145728 ;// 设置附件上传大小
	    $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
	    $upload->rootPath  =     './Uploads/'; // 设置附件上传根目录
	    $upload->savePath  =     'showed/'; // 设置附件上传（子）目录
	    // 上传文件 
	    $info   =   $upload->upload();
	    if(!$info) {// 上传错误提示错误信息
	        $this->error($upload->getError());
	    }else{// 上传成功
	    	$data = array();
	    	foreach ($info as $key => $value) {
	    		$data[] = $value['savepath'].$value['savename'];
	    	}
	    	$data = implode('|', $data);
	    	return $data;	    }
	}

}