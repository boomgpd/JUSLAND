<?php namespace Admin\Controller;
header('Content-Type: text/html; charset=utf-8');
use Think\Controller;
use Admin\Model\PictextModel;
/**
 * 创建媒体图文新闻后台控制器
 * geyu
 * 2016/09/13
 */
class PictextController extends Controller{
	protected $pictext;
	
	
	public function __construct(){
		parent::__construct();
		$this->pictext= new PictextModel;
	}
	/**
	 * 创建前台显示的方法
	 */
	public function index(){
		$data = $this->pictext->getData();
		$arr = array();
		foreach ($data as $key => $value) {
			$att = array();
			$att['box']            = $value;
			$att['box']['addtime'] = date('Y-m-d H:i:s');
			$arr[]                 = $att;
		}
		$this->assign('arr',$arr);
		$this->display();
	} 
	/**
	 * 创建添加的方法
	 */
	public function add(){
		if(IS_POST){
			$data            = I('post.');
			$data['addtime'] = time();
			$data['is_del']  =0;
			$re = $this->pictext->addData($data);
			if($re){
				redirect(U('index'));
			}else{
				echo '<script type="text/javascript">alert("添加失败，请重新添加");</script>';
			}
		}
		$this->display();
	} 
	/**
	 * 创建删除的方法
	 */
	public function del(){
		if(IS_AJAX){
   			$data = I('post.del');
			$re = $this->pictext->delData($data);
   		}
	} 
	/**
	 * 创建编辑的方法
	 */
	public function save(){
		if(IS_POST){
			$data = I('post.');
			$re = $this->pictext->saveData($data);
			if($re){
				redirect(U('index'));
			}else{
				echo '<script type="text/javascript">alert("编辑失败，请重新编辑");</script>';
			}
		}
		$data = I('get.pic_text_id');
		$data = $this->pictext->getData($data);
		$this->assign('data',$data);
		$this->display();
	} 
	/**
	 * 创建显示子页内容的方法
	 */
	public function index_class(){
		$get  = I('get.pic_text_id');
		$data = $this->pictext->getData_class($get);
		$arr = array();
		foreach ($data as $key => $value) {
			$att = array();
			$att['box']            = $value;
			$att['box']['addtime'] = date('Y-m-d H:i:s');
			$arr[]                 = $att;
		}
		$this->assign('arr',$arr);
		$this->display();
	}
	/**
	 * 创建添加子页内容的方法
	 */
	public function add_class(){
		$get = I('get.pic_text_id');
		$this->assign('get',$get);
		if(IS_POST){
			$data            = I('post.');
			$data['addtime'] = time();
			$data['is_del']  = 0;
			$re = $this->pictext->addData_class($data);
			if($re['save']){
				echo '<script type="text/javascript">alert("添加成功");location.href="'.U('add_class',array('pic_text_id'=>$re['pic_text_id'])).'";</script>';
			}else{
				echo '<script type="text/javascript">alert("添加失败，请重新添加");location.href="'.U('add_class',array('pic_text_id'=>$re['pic_text_id'])).'";</script>';
			}
		}
		$this->display();
	} 
	/**
	 * 创建删除子页的方法
	 */
	public function del_class(){
		if(IS_AJAX){
			$data = I('post.del');
			$re = $this->pictext->delData_class($data);
   		}
	} 
	/**
	 * 创建编辑子页的方法
	 */
	public function save_class(){
		if(IS_POST){
			$data = I('post.');
			$re = $this->pictext->saveData_class($data);
			if($re['new_pic_text_content']){
				echo '<script type="text/javascript">alert("编辑成功");location.href="'.U('index_class',array('pic_text_id'=>$re['pic_text_id'])).'";</script>';
			}else{
				echo '<script type="text/javascript">alert("编辑失败，请重新编辑");location.href="'.U('index_class',array('pic_text_content_id'=>$re['pic_text_content_id'])).'";</script>';
			}
		}
		$data = I('get.pic_text_content_id');
		$data = $this->pictext->getData_class_save($data);
		$this->assign('data',$data);
		$this->display();
	}
	
}	