<?php namespace Admin\Controller;
header('Content-Type: text/html; charset=utf-8');
use Think\Controller;
/**
 * 创建媒体图文新闻后台控制器
 * geyu
 * 2016/09/13
 */
class NewsController extends Controller{
//	protected $pictext;
	
	
	public function __construct(){
		parent::__construct();
//		$this->pictext= new PictextModel;
	}
	/**
	 * 创建添加的方法
	 */
	public function add(){
			if($_GET['new_word_id']){//文字类型
				$re['tid'] = I('get.new_word_id');
				$re['htt'] = "Mesdia/index";
				if(IS_POST){
					$data = I('post.');
					if($data['lid']==1){//轮播推荐
						$bool = M('news_index')->where(array('new_recommend_type'=>$data['lid']))->select();
						$info = M('news_index')->where(array('new_recommend_type'=>$data['lid'],'new_id'=>$data['tid'],'new_type'=>1))->find();
						if($info==null && count($bool)<=3){
							$arr = array(
								'new_type'=>1,
								'new_id'  =>$data['tid'],
								'img_src' =>$data['img_src'],
								'new_recommend_type'=>$data['lid']
							);
							$re = M('news_index')->add($arr);
							if(!$re){
								echo '<script type="text/javascript">alert("添加失败")</script>';
							}else{
								echo '<script type="text/javascript">alert("添加成功");location.href = "'.U($data['htt']).'"</script>';
							}
						}else{
							echo '<script type="text/javascript">alert("该新闻以添加,请添加别的新闻吧！已经添加满了");</script>';
						}
					}else if($data['lid']==2){//头条推荐
						$info = M('news_index')->where(array('new_recommend_type'=>$data['lid']))->select();
						if($info==null && count($info)==0){
							$arr = array(
								'new_type'=>1,
								'new_id'  =>$data['tid'],
								'title'  =>$data['title'],
								'new_recommend_type'=>$data['lid']
							);
							$re = M('news_index')->add($arr);
							if(!$re){
								echo '<script type="text/javascript">alert("添加失败")</script>';
							}else{
								echo '<script type="text/javascript">alert("添加成功");location.href = "'.U($data['htt']).'"</script>';
							}
						}else{
							echo '<script type="text/javascript">alert("该新闻以添加,请添加别的新闻吧！只能添加一条头条");</script>';
						}
					}else if($data['lid']==3){//热点推荐
						$bool = M('news_index')->where(array('new_recommend_type'=>$data['lid']))->select();
						$info = M('news_index')->where(array('new_recommend_type'=>$data['lid'],'new_id'=>$data['tid'],'new_type'=>1))->find();
						if($info==null && count($bool)<=5){
							$arr = array(
								'new_type'=>1,
								'new_id'  =>$data['tid'],
								'title'  =>$data['title'],
								'new_recommend_type'=>$data['lid']
							);
							$re = M('news_index')->add($arr);
							if(!$re){
								echo '<script type="text/javascript">alert("添加失败")</script>';
							}else{
								echo '<script type="text/javascript">alert("添加成功");location.href = "'.U($data['htt']).'"</script>';
							}
						}else{
							echo '<script type="text/javascript">alert("该新闻以添加,请添加别的新闻吧！已经添加满了");</script>';
						}
					}else if($data['lid']==4){//最新推荐
						$bool = M('news_index')->where(array('new_recommend_type'=>$data['lid']))->select();
						$info = M('news_index')->where(array('new_recommend_type'=>$data['lid'],'new_id'=>$data['tid'],'new_type'=>1))->find();
						if($info==null && count($bool)<=5){
							$arr = array(
								'new_type'=>1,
								'new_id'  =>$data['tid'],
								'title'  =>$data['title'],
								'new_recommend_type'=>$data['lid']
							);
							$re = M('news_index')->add($arr);
							if(!$re){
								echo '<script type="text/javascript">alert("添加失败")</script>';
							}else{
								echo '<script type="text/javascript">alert("添加成功");location.href = "'.U($data['htt']).'"</script>';
							}
						}else{
							echo '<script type="text/javascript">alert("该新闻以添加,请添加别的新闻吧！已经添加满了");</script>';
						}
					}else if($data['lid']==5){//本周推荐
						$bool = M('news_index')->where(array('new_recommend_type'=>$data['lid']))->select();
						$info = M('news_index')->where(array('new_recommend_type'=>$data['lid'],'new_id'=>$data['tid'],'new_type'=>1))->find();
						if($info==null && count($bool)<=5){
							$arr = array(
								'new_type'=>1,
								'new_id'  =>$data['tid'],
								'title'  =>$data['title'],
								'new_recommend_type'=>$data['lid']
							);
							$re = M('news_index')->add($arr);
							if(!$re){
								echo '<script type="text/javascript">alert("添加失败")</script>';
							}else{
								echo '<script type="text/javascript">alert("添加成功");location.href = "'.U($data['htt']).'"</script>';
							}
						}else{
							echo '<script type="text/javascript">alert("该新闻以添加,请添加别的新闻吧！已经添加满了");</script>';
						}
					}
				}
			}else if($_GET['pic_text_id']){//图文类型
				$re['tid'] = I('get.pic_text_id');
				$re['htt'] = "Pictext/index";
				if(IS_POST){
					$data = I('post.');
					if($data['lid']==1){//轮播推荐
						$bool = M('news_index')->where(array('new_recommend_type'=>$data['lid']))->select();
						$info = M('news_index')->where(array('new_recommend_type'=>$data['lid'],'new_id'=>$data['tid'],'new_type'=>2))->find();
						if($info==null && count($bool)<=3){
							$arr = array(
								'new_type'=>2,
								'new_id'  =>$data['tid'],
								'img_src' =>$data['img_src'],
								'new_recommend_type'=>$data['lid']
							);
							$re = M('news_index')->add($arr);
							if(!$re){
								echo '<script type="text/javascript">alert("添加失败")</script>';
							}else{
								echo '<script type="text/javascript">alert("添加成功");location.href = "'.U($data['htt']).'"</script>';
							}
						}else{
							echo '<script type="text/javascript">alert("该新闻以添加,请添加别的新闻吧！已经添加满了");</script>';
						}
					}else if($data['lid']==2){//头条推荐
						$info = M('news_index')->where(array('new_recommend_type'=>$data['lid']))->select();
						if($info==null && count($info)==0){
							$arr = array(
								'new_type'=>2,
								'new_id'  =>$data['tid'],
								'img_src' =>$data['img_src'],
								'title'  =>$data['title'],
								'new_recommend_type'=>$data['lid']
							);
							$re = M('news_index')->add($arr);
							if(!$re){
								echo '<script type="text/javascript">alert("添加失败")</script>';
							}else{
								echo '<script type="text/javascript">alert("添加成功");location.href = "'.U($data['htt']).'"</script>';
							}
						}else{
							echo '<script type="text/javascript">alert("该新闻以添加,请添加别的新闻吧！只能添加一条头条");</script>';
						}
					}else if($data['lid']==3){//热点推荐
						$bool = M('news_index')->where(array('new_recommend_type'=>$data['lid']))->select();
						$info = M('news_index')->where(array('new_recommend_type'=>$data['lid'],'new_id'=>$data['tid'],'new_type'=>2))->find();
						if($info==null && count($bool)<=5){
							$arr = array(
								'new_type'=>2,
								'new_id'  =>$data['tid'],
								'title'  =>$data['title'],
								'new_recommend_type'=>$data['lid']
							);
							$re = M('news_index')->add($arr);
							if(!$re){
								echo '<script type="text/javascript">alert("添加失败")</script>';
							}else{
								echo '<script type="text/javascript">alert("添加成功");location.href = "'.U($data['htt']).'"</script>';
							}
						}else{
							echo '<script type="text/javascript">alert("该新闻以添加,请添加别的新闻吧！已经满了");</script>';
						}
					}else if($data['lid']==4){//最新推荐
						$bool = M('news_index')->where(array('new_recommend_type'=>$data['lid']))->select();
						$info = M('news_index')->where(array('new_recommend_type'=>$data['lid'],'new_id'=>$data['tid'],'new_type'=>2))->find();
						if($info==null && count($bool)<=5){
							$arr = array(
								'new_type'=>2,
								'new_id'  =>$data['tid'],
								'title'  =>$data['title'],
								'new_recommend_type'=>$data['lid']
							);
							$re = M('news_index')->add($arr);
							if(!$re){
								echo '<script type="text/javascript">alert("添加失败")</script>';
							}else{
								echo '<script type="text/javascript">alert("添加成功");location.href = "'.U($data['htt']).'"</script>';
							}
						}else{
							echo '<script type="text/javascript">alert("该新闻以添加,请添加别的新闻吧！已经满了");</script>';
						}
					}else if($data['lid']==5){//本周推荐
						$bool = M('news_index')->where(array('new_recommend_type'=>$data['lid']))->select();
						$info = M('news_index')->where(array('new_recommend_type'=>$data['lid'],'new_id'=>$data['tid'],'new_type'=>2))->find();
						if($info==null && count($bool)<=5){
							$arr = array(
								'new_type'=>2,
								'new_id'  =>$data['tid'],
								'title'  =>$data['title'],
								'new_recommend_type'=>$data['lid']
							);
							$re = M('news_index')->add($arr);
							if(!$re){
								echo '<script type="text/javascript">alert("添加失败")</script>';
							}else{
								echo '<script type="text/javascript">alert("添加成功");location.href = "'.U($data['htt']).'"</script>';
							}
						}else{
							echo '<script type="text/javascript">alert("该新闻以添加,请添加别的新闻吧！已经满了");</script>';
						}
					}
				}
			}else if($_GET['new_video_id']){//视频类型
				$re['tid'] = I('get.new_video_id');
				$re['htt'] = "videos/index";
				if(IS_POST){
					$data = I('post.');
					if($data['lid']==1){//轮播推荐
						$bool = M('news_index')->where(array('new_recommend_type'=>$data['lid']))->select();
						$info = M('news_index')->where(array('new_recommend_type'=>$data['lid'],'new_id'=>$data['tid'],'new_type'=>3))->find();
						if($info==null && count($bool)<=3){
							$arr = array(
								'new_type'=>3,
								'new_id'  =>$data['tid'],
								'img_src' =>$data['img_src'],
								'new_recommend_type'=>$data['lid']
							);
							$re = M('news_index')->add($arr);
							if(!$re){
								echo '<script type="text/javascript">alert("添加失败")</script>';
							}else{
								echo '<script type="text/javascript">alert("添加成功");location.href = "'.U($data['htt']).'"</script>';
							}
						}else{
							echo '<script type="text/javascript">alert("该新闻以添加,请添加别的新闻吧！已经满了");</script>';
						}
					}else if($data['lid']==2){//头条推荐
						$info = M('news_index')->where(array('new_recommend_type'=>$data['lid']))->select();
						if($info==null && count($info)==0){
							$arr = array(
								'new_type'=>3,
								'new_id'  =>$data['tid'],
								'img_src' =>$data['img_src'],
								'title'  =>$data['title'],
								'new_recommend_type'=>$data['lid']
							);
							$re = M('news_index')->add($arr);
							if(!$re){
								echo '<script type="text/javascript">alert("添加失败")</script>';
							}else{
								echo '<script type="text/javascript">alert("添加成功");location.href = "'.U($data['htt']).'"</script>';
							}
						}else{
							echo '<script type="text/javascript">alert("该新闻以添加,请添加别的新闻吧！");</script>';
						}
					}else if($data['lid']==3){//热点推荐
						$bool = M('news_index')->where(array('new_recommend_type'=>$data['lid']))->select();
						$info = M('news_index')->where(array('new_recommend_type'=>$data['lid'],'new_id'=>$data['tid'],'new_type'=>3))->find();
						if($info==null && count($bool)<=5){
							$arr = array(
								'new_type'=>3,
								'new_id'  =>$data['tid'],
								'title'  =>$data['title'],
								'new_recommend_type'=>$data['lid']
							);
							$re = M('news_index')->add($arr);
							if(!$re){
								echo '<script type="text/javascript">alert("添加失败")</script>';
							}else{
								echo '<script type="text/javascript">alert("添加成功");location.href = "'.U($data['htt']).'"</script>';
							}
						}else{
							echo '<script type="text/javascript">alert("该新闻以添加,请添加别的新闻吧！已经满了");</script>';
						}
					}else if($data['lid']==4){//最新推荐
						$bool = M('news_index')->where(array('new_recommend_type'=>$data['lid']))->select();
						$info = M('news_index')->where(array('new_recommend_type'=>$data['lid'],'new_id'=>$data['tid'],'new_type'=>3))->find();
						if($info==null && count($bool)<=5){
							$arr = array(
								'new_type'=>3,
								'new_id'  =>$data['tid'],
								'title'  =>$data['title'],
								'new_recommend_type'=>$data['lid']
							);
							$re = M('news_index')->add($arr);
							if(!$re){
								echo '<script type="text/javascript">alert("添加失败")</script>';
							}else{
								echo '<script type="text/javascript">alert("添加成功");location.href = "'.U($data['htt']).'"</script>';
							}
						}else{
							echo '<script type="text/javascript">alert("该新闻以添加,请添加别的新闻吧！已经满了");</script>';
						}
					}else if($data['lid']==5){//本周推荐
						$bool = M('news_index')->where(array('new_recommend_type'=>$data['lid']))->select();
						$info = M('news_index')->where(array('new_recommend_type'=>$data['lid'],'new_id'=>$data['tid'],'new_type'=>3))->find();
						if($info==null && count($bool)<=5){
							$arr = array(
								'new_type'=>3,
								'new_id'  =>$data['tid'],
								'title'  =>$data['title'],
								'new_recommend_type'=>$data['lid']
							);
							$re = M('news_index')->add($arr);
							if(!$re){
								echo '<script type="text/javascript">alert("添加失败")</script>';
							}else{
								echo '<script type="text/javascript">alert("添加成功");location.href = "'.U($data['htt']).'"</script>';
							}
						}else{
							echo '<script type="text/javascript">alert("该新闻以添加,请添加别的新闻吧！已经满了");</script>';
						}
					}
				}
			}
		$this->assign('re',$re);
		$this->display();
	}
	/**
	 * 创建显示的方法
	 */	
	public function index(){
		$get = I('get.type');
		if($get){
			$data= M('news_index')->where(array('new_recommend_type'=>$get))->select();
		}else{
			$data= M('news_index')->select();
		}
		$this->assign('data',$data);
		$this->display();
	}
	/*	
	 * 创建删除的方法
	 */
	public function del(){
		if(IS_AJAX){
			$data = I('post.del');
			$re = M("news_index")->where(array('new_recommend_id'=>$data))->delete();
			if($re){
				$this->ajaxReturn($re);die;
			}else{
				echo '<script type="text/javascript">alert("删除失败");</script>';
			}
		}
	}
}