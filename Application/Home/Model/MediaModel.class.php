<?php
namespace Home\Model;
use Think\Model;
/**
 * 创建媒体前台的模型
 * 葛羽
 * 09/26
 */
class MediaModel extends Model {
	protected $tableName = 'area';
	/**
	 * 创建前台推荐显示的方法
	 */							
	public function getData(){
		$re = M('news_index')->where(array('is_del'=>0))->select();
		return $re;
	}
	/**
	 * 创建首页图文的显示的方法
	 */
	public function img_textData(){
		$re = M('new_text_pic_index')->alias('a')->join('__NEW_PIC_TEXT__ b ON a.text_pic_id = b.pic_text_id')->where(array('is_del'=>0))->select();
		return $re;
	}
	/**
	 * 创建前台页面推荐信息的方法
	 */						
	public function infoData($data){
		if($data['type'] == 1){
			if($data['id']!=null){
				$re  = M('new_article')->where(array('new_word_id'=>$data['id'],'is_del'=>0))->find();
				$arr = array(
					'top_big_bananer' => $re['top_big_bananer'],
					'middel_small_bananer' => $re['middel_small_bananer'],
					'left_easy_title' => $re['left_easy_title'],
					'left_easy_content' => explode('|',$re['left_easy_content']),
					'right_main_title' => $re['right_main_title'],
					'right_des_title' => $re['right_des_title'],
					'article_content' => htmlspecialchars_decode($re['article_content']),
					'addtime'         => date("Y.m.d",$re['addtime'])
				); 
			}else{
				$re  = M('new_article')->where(array('is_del'=>0))->select();
				$arr = array();
				foreach ($re as $key => $value) {
					$att = array();
					$att['box'] = $value;
					$arr[] = $att;
				}
			}
		}else if($data['type'] == 2){
			$arr  = M('new_pic_text_content')->where(array('pic_text_id'=>$data['id'],'is_del'=>0))->select();
		}else if($data['type'] == 3){
			if($data['id'] != null){
				$re  = M('new_video')->where(array('is_del'=>0))->select();
				$arr = array();
				foreach ($re as $key => $value) {
					$att = array();
					$att['box'] = $value;
//					$att['list'] = $list;
					$arr[] = $att;
				}
			}else{
				$re  = M('new_video')->where(array('is_del'=>0))->select();
				$arr = array();
				foreach ($re as $key => $value) {
					$att = array();
					$att['box'] = $value;
					$arr[] = $att;
				}
			}
		}
		return $arr;
	}
	/**
	 * 创建视频分类的显示方法
	 */
	public function videosData(){
		$re = M('new_video_type')->where(array('is_del'=>0))->select();
		$arr = array();
		foreach ($re as $key => $value) {
			$data['box'] = $value;
			$data['box']['new_video'] = M('new_video')->where(array('new_video_type_id'=>$value['new_video_type_id'],'is_del'=>0))->select();
			$arr[] = $data;
		}
		return $arr;
	}
	/**	
	 * 创建列表页面显示的方法
	 */
	public function list_media($data){
		if($data['type']==null){
			$text = M('new_article')->field('list_img,new_word_id,click_num,left_easy_title as title,keyword')->where(array('is_del'=>0))->select();
			$img_tetx = M('new_pic_text')->field('list_img,pic_text_id,click_num,title as title,keyword')->where(array('is_del'=>0))->select();
			$videos = M('new_video')->field('list_img,new_video_id,click_num,video_title as title,keyword')->where(array('is_del'=>0))->select();
			if($text == null){
				$arr =  array_merge($img_tetx,$videos);
			}else if($img_tetx == null){
				$arr =  array_merge($text,$videos);
			}else if($videos == null){
				$arr =  array_merge($text,$img_tetx);
			}else if($videos || $img_tetx || $text){
				$arr =  array_merge($text,$img_tetx,$videos);
			}
		}else if($data['type']==1){//文字
			$arr = M('new_article')->field('list_img,new_word_id,click_num,left_easy_title as title,keyword')->where(array('is_del'=>0))->select();
		}else if($data['type']==2){//图文
			$arr = M('new_pic_text')->field('list_img,pic_text_id,click_num,title as title,keyword')->where(array('is_del'=>0))->select();
		}else if($data['type']==3){//视频
			$arr = M('new_video')->field('list_img,new_video_id,click_num,video_title as title,keyword')->where(array('is_del'=>0))->select();
		};
		$q = $data['q'];
		if(!empty($data['q'])){
			$temp = array();
			foreach($arr as $k => $v){
				if(strpos($v['title'],$q) !== FALSE || strpos($v['keyword'],$q) !== FALSE){
					$temp[] = $v;
				};
			};
			$arr = $temp;
		};
		return $arr;
	}
	/**
	 * 创建列表页点赞的方法
	 */
	public function hlepData($data){
		switch ($data['type']) {
			case '1':
				$is_clicl = M('new_click')->where(array('new_type'=>$data['type'],'new_id'=>$data['id'],'member_id'=>$_SESSION['member_id']))->find();
				if($is_clicl && $_SESSION['member_id']){
					$re   = M('new_click')->where(array('new_type'=>$data['type'],'new_id'=>$data['id']))->delete();
					$save = M('new_article')->where(array('new_word_id'=>$data['id']))->setDec('click_num',1);
					if($re && $save){$arr = array('type' => 0,'content'=>'取消点赞');}
				}else{
					$att  = array('member_id' => $_SESSION['member_id'],'new_id'    => $data['id'],'new_type'  => $data['type']);
					$re   = M('new_click')->add($att);
					$save = M('new_article')->where(array('new_word_id'=>$data['id']))->setInc('click_num',1);
					if($re && $save){$arr  = array('type' => 1,'content'=>"点赞完成");}
				}
				break;
			case '2':
				$is_clicl = M('new_click')->where(array('new_type'=>$data['type'],'member_id'=>$_SESSION['member_id'],'new_id'=>$data['id']))->find();
				if($is_clicl && $_SESSION['member_id']){
					$re   = M('new_click')->where(array('new_type'=>$data['type'],'new_id'=>$data['id']))->delete();
					$save = M('new_pic_text')->where(array('pic_text_id'=>$data['id']))->setDec('click_num',1);
					if($re && $save){$arr = array('type' => 0,'content'=>'取消点赞');}
				}else{
					$att  = array('member_id' => $_SESSION['member_id'],'new_id'=> $data['id'],'new_type'  => $data['type']);
					$re   = M('new_click')->add($att);
					$save = M('new_pic_text')->where(array('pic_text_id'=>$data['id']))->setInc('click_num',1);
					if($re && $save){$arr  = array('type' => 1,'content'=>"点赞完成");}
				}
				break;
			case '3':
				$is_clicl = M('new_click')->where(array('new_type'=>$data['type'],'member_id'=>$_SESSION['member_id'],'new_id'=>$data['id']))->find();
				if($is_clicl && $_SESSION['member_id']){
					$re   = M('new_click')->where(array('new_type'=>$data['type'],'new_id'=>$data['id']))->delete();
					$save = M('new_video')->where(array('new_video_id'=>$data['id']))->setDec('click_num',1);
					if($re && $save){$arr = array('type' => 0,'content'=>'取消点赞');}
				}else{
					$att  = array('member_id' => $_SESSION['member_id'],'new_id'    => $data['id'],'new_type'  => $data['type']);
					$re   = M('new_click')->add($att);
					$save = M('new_video')->where(array('new_video_id'=>$data['id']))->setInc('click_num',1);
					if($re && $save){$arr  = array('type' => 1,'content'=>"点赞完成");}
				}
				break;
		}
		
		return $arr;
	}
	/*
	 * 创建首页推荐
	 */
	public function indexData(){
		$re1 = M('new_index_list')->where(array('is_bananer'=>1,'is_del'=>0))->limit(4)->select();
		$re2 = M('new_index_list')->where(array('is_special'=>1,'is_del'=>0))->limit(4)->select();
		$re3 = M('new_index_list')->where(array('is_height'=>1,'is_del'=>0))->limit(1)->find();
		$re4 = M('new_index_list')->where(array('is_lack'=>1,'is_del'=>0))->limit(1)->find();
		$arr = array(
			'bananer' =>$re1,
			'special' =>$re2,
			'height'  =>$re3,
			'lack'    =>$re4
		);
		return $arr;
	}
}
?>