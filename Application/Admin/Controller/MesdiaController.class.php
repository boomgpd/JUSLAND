<?php namespace Admin\Controller;
use Think\Controller;
use Admin\Model\MesdiaModel;
	/**
	 * 创建媒体子页面新闻图文的控制器
	 * 9.8
	 * 葛羽
	 */
class MesdiaController extends Controller {
	protected $mesdia;
	
	public function __construct(){
	 	parent::__construct();
		
		$this-> mesdia = new MesdiaModel;
	}
	public function seion(){
		$admin_id = 1;
		session('admin_id',$admin_id);
	}
	/**
	 * 创建添加的放法
	 */
    public function add(){
    	if(IS_POST){
    		$data = I('post.');
			$re = $this->mesdia->addDatd($data);
			if(!$re){
				alert($this->mesdia->getError());
			}else{
				alert('添加成功');
			}
    	}
    	$this->display();
    }
	/**
	 * 创建页面显示的放法
	 */
	public function index(){
		$att = $this->mesdia->getData();
		$this->assign('att',$att);
		$this->display();
	}
	/**
	 * 创建删除的放法
	 */
	public function del_mesdia(){
		if(IS_AJAX){
			$data = I('post.del');
			$re = M('new_article')->where(array('new_word_id'=>$data))->save(array('is_del'=>1));
			$data = M('news_index')->where(array('new_id'=>$data))->delete();
			$this->ajaxReturn($re);exit;
		}
	}
	/**
	 * 创建编辑的方法
	 */
	public function save_mesdia(){
		if(IS_POST){
			$data = I('post.');
			$att  = array(
			 	'left_easy_content'    => explode("\n",$data['left_easy_content']),
			);
			$arr = array(
				'new_word_id'          => $data['new_word_id'],
				'admin_id'             => $_SESSION['admin_id'],
				'addtime'              => time(),
			 	'top_big_bananer'      => implode("|",$data['top_big_bananer']),
			 	'middel_small_bananer' => implode("|",$data['middel_small_bananer']),
			 	'list_img'             => implode("|",$data['list_img']),
				'left_easy_title'      => $data['left_easy_title'],
			 	'left_easy_content'    => implode("|",$att['left_easy_content']),
			 	'right_main_title'     => $data['right_main_title'],
			 	'right_des_title'      => $data['right_des_title'],
			 	'article_content'      => $data['article_content'],
			 	'show_title'              => $data['show_title'],
			 	'keyword'              => $data['keyword']
			);
			$re = M('new_article')->where(array('new_word_id'=>$data['new_word_id']))->save($arr);
			if($re){
				echo '<script type="text/javascript">alert("更新成功");location.href="'.U('index').'";</script>';
			}else{
				echo '<script type="text/javascript">alert("更新失败");</script>';
			}
		}
		$data = M('new_article')->where(array('is_del'=>0,'new_word_id'=>$_GET['new_word_id']))->find();
			$arr = array();
			$arr['box']                         = $data;
			$arr['box']['top_big_bananer']      = explode("|", $data['top_big_bananer']);
			$arr['box']['middel_small_bananer'] = explode("|", $data['middel_small_bananer']);
			$arr['box']['list_img'] = explode("|", $data['list_img']);
			$left_easy_content                  = explode("|", $data['left_easy_content']);
			$arr['box']['left_easy_content']    = implode("\n", $left_easy_content);
			$arr['box']['article_content']      = htmlspecialchars_decode($data['article_content']);
		$this->assign('arr',$arr);
		$this->assign('get',$_GET);
		$this->display();
	}
	/**
	 * 创建处理编辑的方法
	 */
	public function haha(){
		dump($_POST);die;
	}
}