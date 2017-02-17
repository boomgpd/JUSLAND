<?php namespace Admin\Model;
use Think\Model;
header('Content-Type: text/html; charset=utf-8');
/**
 * 创建媒体图文新闻的模型
 * geyu
 * 9.13
 */
class PictextModel extends Model{
	
	protected $tableName="new_pic_text";
	
	protected $_validate = array(
     array('list_img','require','不能为空'),
     array('title','require','不能为空'),
   );
   /**
    * 创建添加的方法
    */
   public function addData($data){
   	   	$re = $this->create($data);
		if(!$re) return FALSE;
		$re = $this->add($this->$data);
		return $re;
	
   }
    /**
    * 创建显示的方法
    */
   public function getData($data){
   		if($data){
			$re = $this->where(array('is_del'=>0,'pic_text_id'=>$data))->find();
   		}else{
			$re = $this->where(array('is_del'=>0))->select();
   		}
		return $re;
   }
    /**
    * 创建编辑的方法
    */
   public function saveData($data){
   		$arr = array(
			'title'   => $data['title'],
			'list_img'=> $data['list_img'],
			'addtime' => time(),
			'is_del'  => 0
		);
   		$re  = $this->where(array('pic_text_id'=>$data['pic_text_id']))->save($arr);
   		return $re;
   }
    /**
    * 创建删除的方法
    */
   public function delData($data){
   		$re['new_pic_text']         = $this->where(array('pic_text_id'=>$data))->save(array('is_del'=>1));
   		$re['new_pic_text_content'] = M('new_pic_text_content')->where(array('pic_text_id'=>$data))->save(array('is_del'=>1));
   		$re['news_index'] = M('news_index')->where(array('new_id'=>$data))->delete();
  		return $re;
   }
   
   /**
	 * 创建显示子页内容的方法
	 */
	public function getData_class($data){
		$re = M('new_pic_text_content')->where(array('is_del'=>0,'pic_text_id'=>$data))->select();
		return $re;
	}
	/**
	 * 创建编辑子页的显示方法
	 */
	public function getData_class_save($data){
		$re = M('new_pic_text_content')->where(array('is_del'=>0,'pic_text_content_id'=>$data))->find();
		return $re;
	} 
	/**
	 * 创建添加子页内容的方法
	 */
	public function addData_class($data){
		$arr = array(
			'pic_src'      => $data['pic_src'],
			'text_content' => $data['text_content'],
			'pic_text_id'  => $data['pic_text_id'],
			'addtime'      => $data['addtime'],
			'is_del'       => $data['is_del']
		);
		$re['save'] = M('new_pic_text_content')->add($arr);
		$re['pic_text_id'] = $data['pic_text_id'];
		return $re;
	}
	/**
    * 创建编辑子页的方法
    */
   	public function saveData_class($data){
   		$arr = array(
			'text_content'=> $data['text_content'],
			'pic_src'     => $data['pic_src'],
			'addtime'     => time(),
			'is_del'      => 0,
			'pic_text_id' =>$data['pic_text_id']
		);
   		$re['new_pic_text_content'] = M('new_pic_text_content')->where(array('pic_text_content_id'=>$data['pic_text_content_id']))->save($arr);
   		$re['pic_text_id'] = $data['pic_text_id'];
		$re['pic_text_content_id'] = $data['pic_text_content_id'];
   		return $re;
   	}
    /**
    * 创建删除子页的方法
    */
   	public function delData_class($data){
   		$re['new_pic_text_content'] = M('new_pic_text_content')->where(array('pic_text_content_id'=>$data))->save(array('is_del'=>1));
  		return $re;
  	}  
}