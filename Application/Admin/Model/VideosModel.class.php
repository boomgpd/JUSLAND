<?php namespace Admin\Model;
use Think\Model;
/**
 * 创建视频模型
 * 16/09/11
 * geyu
 */
class VideosModel extends Model{
	protected $tableName="new_video";
	protected $_validate = array(
     array('type_name','require','验证码必须！'), //默认情况下用正则进行验证
     array('type_des','require','验证码必须！'), //默认情况下用正则进行验证
     array('is_del','require','验证码必须！'), //默认情况下用正则进行验证
     array('video_title','require','不能为空！'), //默认情况下用正则进行验证
     array('video_des','require','不能为空！'), //默认情况下用正则进行验证
     array('video_url','require','不能为空！'), //默认情况下用正则进行验证
     array('video_width_img','require','不能为空！'), //默认情况下用正则进行验证
     array('video_height_img','require','不能为空！'), //默认情况下用正则进行验证
     array('new_video_type_id','require','不能为空！'), //默认情况下用正则进行验证
     array('addtime','require','不能为空！'), //默认情况下用正则进行验证
     array('is_bananer','require','不能为空！'), //默认情况下用正则进行验证
     array('is_hot','require','不能为空！'), //默认情况下用正则进行验证
     array('list_img','require','不能为空！'), //默认情况下用正则进行验证
   );
   /**
    * 创建视频添加的方法
    */
	public function addData($data){
		if(!$data['type_name'] && !$data['type_des']){//这里是视频添加
			$re = M('new_video')->create($data);
			if(!$re) return FALSE;
			$re = M('new_video')->add($this->$data);
//			dump($re);die;
			return $re;
		}else{//这里是视频分类添加的
			$arr = array(
			 	'type_name'=>$data['type_name'],
			 	'type_des' =>$data['type_des'],
			 	'id_del'   =>0
			);
			$re = M('new_video_type')->create($arr);
			if(!$re) return FALSE;
			$re = M('new_video_type')->add($arr);
			return $re;
		}
	}
	public function type(){
		$re = M('new_video_type')->where(array('is_del'=>0))->select();
		return $re;
	}
	/**
	 * 创建前台显示的方法
	 */
	public function getData($data){
		if($data){
			if($data['type_id']){
				$where = array('is_del'=>0,'new_video_type_id'=>$data['type_id']);
			}else if($data['is_bananer']){
				$where = array('is_del'=>0,'is_bananer'=>$data['is_bananer']);
			}else if($data['is_hot']){
				$where = array('is_del'=>0,'is_hot'=>$data['is_hot']);
			}
		}else{
			$where = array('is_del'=>0);
		}
		$re = M('new_video')->where($where)->select();
		return $re;
	}
	public function sava_show($data){
		$re = M('new_video')->where(array('is_del'=>0,'new_video_id'=>$data['new_video_id']))->find();
		return $re;
	}
	/**
	 * 创建编辑的方法
	 */
	 public function saveData($data){
	 	if(!$data['type_name'] && !$data['type_des']){
	 		$re = M('new_video')->create($data);
			if(!$re) return FALSE;
			$re = M('new_video')->where(array('new_video_id'=>$data['new_video_id']))->save($this->$data);
			return $re;
	 	}else{
		 	$re = M('new_video_type')->create($data);
			if(!$re) return FALSE;
			$re = M('new_video_type')->where(array('new_video_type_id'=>$data['new_video_type_id']))->save($this->$data);
			return $re;
	 	}
	 }
	/**
	  * 创建删除的方法
	  */
	public function delData($data){
		if(!$data['new_video_type_id']){
			$arr = array(
				'is_del'=>1
			); 
		  	$re = M('new_video')->where(array('new_video_id'=>$data['new_video_id']))->save(array('is_del'=>1));
		  	$da = M('news_index')->where(array('new_id'=>$data['new_video_id']))->delete();
			return $re;
		}else{
		  	$arr = array(
				'is_del'=>1
			);
		  	$re = M('new_video_type')->where(array('new_video_type_id'=>$data['new_video_type_id']))->save(array('is_del'=>1));
			return $re;
		}
	  }
}
