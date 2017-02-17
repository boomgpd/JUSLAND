<?php namespace Admin\Model;
use Think\Model;
header('Content-Type: text/html; charset=utf-8');
/**
 * 创建媒体子页面新闻图文的模型
 * geyu
 * 9.2
 */
class MesdiaModel extends Model{
	
	protected $tableName="new_article";
	
	protected $_validate = array(
     array('admin_id','require','不能为空',1),
     array('addtime','require','不能为空',1), 
     array('top_big_bananer','require','不能为空',1), 
     array('middel_small_bananer','require','不能为空',1), 
     array('left_easy_title','require','不能为空',1), 
     array('left_easy_content','require','不能为空',1), 
     array('right_main_title','require','不能为空',1), 
     array('right_des_title','require','不能为空',1), 
     array('article_content','require','不能为空',1), 
     array('keyword','require','不能为空',1), 
   );
   /**
    * 创建添加
    */
   public function addDatd($data){
   			$att  = array(
			 	'left_easy_content'    => explode("\n",$data['left_easy_content']),
			);
//			$re = implode('|',$att['left_easy_content']);
			$arr = array(
				'admin_id'             => $_SESSION['admin_id'],
				'addtime'              => time(),
			 	'top_big_bananer'      => implode("|",$data['top_big_bananer']),
			 	'middel_small_bananer' => implode("|",$data['middel_small_bananer']),
			 	'list_img'             => implode("|",$data['list_img']),
				'left_easy_title'      => $data['left_easy_title'],
			 	'left_easy_content'    => implode("|",$att['left_easy_content']),
			 	'right_main_title'     => $data['right_main_title'],
			 	'right_des_title'      => $data['right_des_title'],
			 	'article_content'      => $data['intro'],
			 	'keyword'              => $data['keyword'],
			 	'show_title'              => $data['show_title']
			);
   		$re = $this->create($arr);
		if(!$re) return FALSE;
		$re = $this->add($this->data);
		return $re;
   }
   /**
    * 创建显示
    */
   public function getData(){
   		$data['new_article'] = M('new_article')->where(array('is_del'=>0))->select();
		$att = array();
		foreach ($data['new_article'] as $key=>&$value) {
			$arr = array();
			$arr['box'] = $value;
			$arr['box']['top_big_bananer']      = explode("|", $value['top_big_bananer']);
			$arr['box']['middel_small_bananer'] = implode("|", $value['middel_small_bananer']);
			$left_easy_content    = explode("|", $value['left_easy_content']);
			$arr['box']['left_easy_content']    = implode("\n", $left_easy_content);
			$arr['box']['article_content']      = explode("|", $value['article_content']);
			$arr['box']['addtime']              = date('Y-m-d H:i:s');
			$att[] = $arr;
		}
		return $att;
   }
}