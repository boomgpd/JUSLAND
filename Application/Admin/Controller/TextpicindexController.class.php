<?php namespace Admin\Controller;
header('Content-Type: text/html; charset=utf-8');
use Think\Controller;
use Admin\Model\PictextModel;
/**
 * 创建首页推荐控制器
 * 16/09/13
 * geyu
 */
class TextpicindexController extends CommonController {
	/**
	 * 创建成员属性
	 */
	 protected $pictext;
	
	/**
	 * 创建构造函数
	 */
	public function __construct(){
		parent::__construct();
		$this->pictext = new PictextModel;
	}
	/**
	 * 1号推荐
	 * 创建显示的方法
	 */
	public function index(){
		$re = M('new_text_pic_index')->select();
		$arr = array();
		foreach ($re as $key => $value) {
			$att = array();
			$att['title'] = M("new_pic_text")->where(array('pic_text_id'=>$value['text_pic_id']))->getField('title');
			$arr[] = $att;
		}
		$this->assign('arr',$arr);
		$this->assign('re',$re);
		$this->display();
	}
	/**
	 * 创建编辑的方法
	 */
	public function edit(){
		if(IS_POST){
			$data = I('post.');
			$re   = M('new_text_pic_index')->where(array('text_pic_index_id'=>$data['text_pic_index_id']))->save($data);
			if($re){
				redirect(U('index'));
			}else{
				echo '<script type="text/javascript">alert("编辑失败，请重新编辑");</script>';
			}
		}
		$data = I('get.text_pic_index_id');
		$data = M('new_text_pic_index')->where(array('text_pic_index_id'=>$data))->find();
		$this->assign('data',$data);
		$this->display();
		
	}
	/**
	 * 创建选择图文新闻内容对应的方法
	 */
	public function choice(){
		if(IS_POST){
			$data = I('post.');
			if(count($data['li'])==1){
				$arr['text_pic_id']=implode(',', $data['li']);
				$re  = M("new_text_pic_index")->where(array('text_pic_index_id'=>$data['text_pic_index_id']))->setField($arr);
				if($re){
					echo '<script type="text/javascript">alert("选取成功");location.href="'.U('index').'";</script>';
				}else{
					echo '<script type="text/javascript">alert("选取失败，请重新选取");</script>';
				}
			}else{
				echo '<script type="text/javascript">alert("最多选取一条，请重新选取");location.href="'.U('choice',array('text_pic_index_id'=>$data['text_pic_index_id'])).'";</script>';
			}
		}
		$re = I('get.text_pic_index_id');
		$this->assign('re',$re);
		$in = M('new_text_pic_index')->getField('text_pic_id',true);
		$id = implode(',', $in);
		$where['pic_text_id'] = array('not in',$id);
		$data = M('new_pic_text')->where($where)->select();
		$arr = array();
		foreach ($data as $key => $value) {
			$att = array();
			$att['box']            = $value;
			$att['box']['addtime'] = date('Y-m-d H:i:s',$value['addtime']);
			$arr[]                 = $att;
		}
		$this->assign('arr',$arr);
		$this->display();
	}
	/**
	 * 创建选择图文新闻内容对应的方法的子方法
	 */
	public function check(){
		if(IS_POST){
			$data = I('post.');
			if(count($data['li'])==1){
				$att = array('text_pic_id'=>implode(',', $data['li']));
				dump($att);
			}else{
				echo '<script type="text/javascript">alert("最多选取一条，请重新选取");location.href="'.U('choice',array('text_pic_index_id'=>$data['text_pic_index_id'])).'";</script>';
			}
		}
		
	}
	
	
	
	
	
	
	
	/*
	 * 2号推荐
	 * 创建首页推荐显示的方法
	 */
	public function index_list(){
		$get = I('get.');
		if($get['type'] == 1 || $get['yang'] == 1){
			if($get['type']){
				$arr = M('new_index_list')->where(array('news_type'=>1,'is_del'=>0))->select();
			}else if($get['yang']){
				$arr = M('new_index_list')->where(array('is_bananer'=>1,'is_del'=>0))->select();
			}
		}else if($get['type'] == 2 || $get['yang'] == 2){
			if($get['type']){
				$arr = M('new_index_list')->where(array('news_type'=>2,'is_del'=>0))->select();
			}else if($get['yang']){
				$arr = M('new_index_list')->where(array('is_special'=>1,'is_del'=>0))->select();
			}
		}else if($get['type'] == 3 || $get['yang'] == 3){
			if($get['type']){
				$arr = M('new_index_list')->where(array('news_type'=>3,'is_del'=>0))->select();
			}else if($get['yang']){
				$arr = M('new_index_list')->where(array('is_height'=>1,'is_del'=>0))->select();
			}
		}else if($get['yang'] == 4){
			$arr = M('new_index_list')->where(array('is_lack'=>1,'is_del'=>0))->select();
		}else if($get['type'] == 0 || $get['yang'] == 0){
			$arr = M('new_index_list')->where(array('is_del'=>0))->select();
		}
		$this->assign('arr',$arr);
		$this->display();
	}
	/**
	 * 创建添加2号的方法
	 */
	public function add_list(){
		if(IS_POST){
			$data = I('post.');
			if($data['is_bananer']){
				$arr = M('new_index_list')->where(array('is_bananer'=>$data['is_bananer'],'is_del'=>0))->select();
				if(count($arr)<4){
					$re   = M('new_index_list')->add($data);
					if($re){
						echo '<script type="text/javascript">alert("添加成功");location.href="'.U('index_list').'";</script>';
					}else{
						echo '<script type="text/javascript">alert("失败");location.href="'.U('add_list').'";</script>';
					}
				}else{
					echo '<script type="text/javascript">alert("轮播图只可以添加四个，你以添加够了，请添加别的类型");location.href="'.U('add_list').'";</script>';
				}
			}else if($data['is_special']){
				$arr = M('new_index_list')->where(array('is_special'=>$data['is_special'],'is_del'=>0))->select();
				if(count($arr)<4){
					$re   = M('new_index_list')->add($data);
					if($re){
						echo '<script type="text/javascript">alert("添加成功");location.href="'.U('index_list').'";</script>';
					}else{
						echo '<script type="text/javascript">alert("失败");location.href="'.U('add_list').'";</script>';
					}
				}else{
					echo '<script type="text/javascript">alert("专题图只可以添加四个，你以添加够了，请添加别的类型");location.href="'.U('add_list').'";</script>';
				}
			}else if($data['is_height']){
				$arr = M('new_index_list')->where(array('is_height'=>$data['is_height'],'is_del'=>0))->select();
				if(count($arr)<1){
					$re   = M('new_index_list')->add($data);
					if($re){
						echo '<script type="text/javascript">alert("添加成功");location.href="'.U('index_list').'";</script>';
					}else{
						echo '<script type="text/javascript">alert("失败");location.href="'.U('add_list').'";</script>';
					}
				}else{
					echo '<script type="text/javascript">alert("大图只可以添加1个，你以添加够了，请添加别的类型");location.href="'.U('add_list').'";</script>';
				}
			}else if($data['is_lack']){
				$arr = M('new_index_list')->where(array('is_lack'=>$data['is_lack'],'is_del'=>0))->select();
				if(count($arr)<1){
					$re   = M('new_index_list')->add($data);
					if($re){
						echo '<script type="text/javascript">alert("添加成功");location.href="'.U('index_list').'";</script>';
					}else{
						echo '<script type="text/javascript">alert("失败");location.href="'.U('add_list').'";</script>';
					}
				}else{
					echo '<script type="text/javascript">alert("小图只可以添加1个，你以添加够了，请添加别的类型");location.href="'.U('add_list').'";</script>';
				}
			}
		}
		$this->display();
	}
	/*
	 * 创建接收2号添加发过来的ajax
	 */
	public function show_list(){
		if(IS_AJAX){
			$data = I('post.');
			if($data['del'] == 1){//文字
				$in = M('new_index_list')->where(array('news_type'=>1))->getField('new_id',true);
				$id = implode(',', $in);
				$where['is_del'] = 0;
				if($id==null){
					$data = M('new_article')->where($where)->select();
				}else{
					$where['new_word_id'] = array('not in',$id);
					$data = M('new_article')->where($where)->select();
				}
			}else if($data['del'] == 2){//图文
				$in = M('new_index_list')->where(array('news_type'=>2))->getField('new_id',true);
				$id = implode(',', $in);
				$where['is_del'] = 0;
				if($id==null){
					$data = M('new_pic_text')->where($where)->select();
				}else{
					$where['pic_text_id'] = array('not in',$id);
					$data = M('new_pic_text')->where($where)->select();
				}
			}else if($data['del'] == 3){//视频
				$in = M('new_index_list')->where(array('news_type'=>3))->getField('new_id',true);
				$id = implode(',', $in);
				$where['is_del'] = 0;
				if($id==null){
					$data = M('new_video')->where($where)->select();
				}else{
					$where['new_video_id'] = array('not in',$id);
					$data = M('new_video')->where($where)->select();
				}
			}
			$arr = array();
			foreach ($data as $key => $value) {
					$att = array();
					$att            = $value;
					$att['addtime'] = date('Y-m-d H:i:s',$value['addtime']);
					$arr[]                 = $att;
				}
			if($arr){
				$this->ajaxReturn($arr);
			}	
		}
	}
	/*
	 * 创建删除2号的方法
	 */
	public function del(){
		if(IS_AJAX){
			$data = I('post.del');
			$re   = M('new_index_list')->where(array('new_index_list_id'=>$data))->save(array('is_del'=>1));
			if($re){
				$this->ajaxReturn($re);
			}
		}
	}
	/*
	 * 创建编辑2号的方法
	 */
	public function edit_list(){
		if(IS_POST){
			$data = I('post.');
			if($data['is_bananer']){
				$re = M('new_index_list')->where(array('new_index_list_id'=>$data['new_index_list_id']))->save(array('img_src'=>$data['img_src']));
			}else if($data['is_special']){
				$re = M('new_index_list')->where(array('new_index_list_id'=>$data['new_index_list_id']))->save(array('img_src'=>$data['img_src'],'title'=>$data['title']));
			}else if($data['is_height']){
				$re = M('new_index_list')->where(array('new_index_list_id'=>$data['new_index_list_id']))->save(array('img_src'=>$data['img_src']));
			}else if($data['is_lack']){
				$re = M('new_index_list')->where(array('new_index_list_id'=>$data['new_index_list_id']))->save(array('img_src'=>$data['img_src']));
			}
			if($re){
				echo '<script type="text/javascript">alert("编辑成功");location.href="'.U('index_list').'";</script>';
			}else{
				echo '<script type="text/javascript">alert("失败");location.href="'.U('edit_list').'";</script>';
			}
		}
		$get  = I('get.');
		$data = M('new_index_list')->where(array('new_index_list_id'=>$get['new_index_list_id']))->find();
//		dump($data);die;
		$this->assign('data',$data);
		$this->display();
	}
}