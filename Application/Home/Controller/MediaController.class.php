<?php namespace Home\Controller;
use Think\Controller;
use Common\Model\AreaModel;
use Home\Model\MemberModel;
use Home\Model\MediaModel;
/**
 * 前台媒体控制器
 * geyu
 * 09/26
 */
class MediaController extends CommonController {
	protected $area;
	protected $member;
	protected $media;
	public function __construct (){
		parent::__construct();
		$this-> member   	= new MemberModel;
		$this-> area    	= new AreaModel;
		$this-> media    	= new MediaModel;
	}
	public function index(){
		$type   = array('type'=>1);
		$arr    = $this->media->infoData($type);
		$this->assign('arr',$arr);
		
		$vide   = array('type'=>3);
		$videos = $this->media->infoData($vide);
		$this->assign('videos',$videos);
		
		$re   = $this->media->img_textData();
		$this->assign('re',$re);
		
		$list = $this->media->getData();
		$this->assign('list',$list);
		
		foreach ($list as $key => $value) {
			$arr = $this->get_right_data($value['new_type'],$value['new_recommend_type'],$value['new_id']);
			$key_name = key($arr);
			$data[$key_name][] = current($arr);
		}
		$this->assign('data',$data);
		$this->display();
	} 
	/**	
	 * 创建文字页面
	 */
	public function text(){
		if(IS_GET){
			$re = I('get.'); 
			if($re['type'] == 1){//去往文字页的信息
				$re = $this->media->infoData($re);
			}
		}
		$this->assign('re',$re);
		$this->display();
	}
	/**
	 * 获取jsapi的ticket
	 */
	public function wx_get_jsapi_ticket(){
    $ticket = "";
    do{
        $ticket = S('wx_ticket');
        if (!empty($ticket)) {
            break;
        }
        $token = S('access_token');
        if (empty($token)){
            wx_get_token();
        }
        $token = S('access_token');
        if (empty($token)) {
            logErr("get access token error.");
            break;
        }
        $url2 = sprintf("https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=%s&type=jsapi",
            $token);
        $res = file_get_contents($url2);
        $res = json_decode($res, true);
        $ticket = $res['ticket'];
        // 注意：这里需要将获取到的ticket缓存起来（或写到数据库中）
        // ticket和token一样，不能频繁的访问接口来获取，在每次获取后，我们把它保存起来。
        S('wx_ticket', $ticket, 3600);
    }while(0);
    return $ticket;
}
	
	
	/**
	 * 创建获取微信
	 */
	public function wx_get_token() {
    $token = S('access_token');
    if (!$token) {
        $res = file_get_contents('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx949e6c2ead7cd9fc&secret=8a018f5ddd23bc3d92781b98cfe0ff4f');
        $res = json_decode($res, true);
        $token = $res['access_token'];
        S('access_token', $token, 3600);
    }
    return $token;
}
	
	/**	
	 * 创建图文页面
	 */
	public function img_text(){//去往图文页信息
		if(IS_GET){
			$re = I('get.');
			if($re['type'] == 2){
				$data = M('new_pic_text')->where(array('pic_text_id'=>$re['id']))->find();
				$re = $this->media->infoData($re);
			}
		}
		$data['keyword'] = explode('#',$data['keyword']);
		$this->assign('data',$data);
		$this->assign('re',$re);
		$list = M('new_pic_text')->limit('4')->select();
		$this->assign('list',$list);
		$this->display();
	}
	/**	
	 * 创建视频页面
	 */
	public function videos(){//去往视频页信息
		if(IS_GET){
			$re = I('get.'); 
			$list  = M('new_video')->where(array('new_video_id'=>$re['id']))->find();
			$this->assign('list',$list);
			if($re['type'] == 3){
				$re = $this->media->infoData($re);
			}
		}
//		dump($list);die;
		$this->assign('re',$re);
		$data = $this->media->videosData();
		$this->assign('data',$data);
		$this->display();
	}
	/**
	 * 创建列表页的显示方法
	 */
	public function list_data(){
		$get = I('get.');
		$data = $this->media->list_media($get);
		$array = array('0'=>1,'1'=>2,'2'=>3);
		foreach ($array as $key => $value) {
			$click['type'.($key+1)]= M('new_click')->where(array('new_type'=>$value))->select();
		}
		foreach ($click['type1'] as $key => $value) {
			$text[] = $value['new_id'];
		}
		$text = implode(',',$text);
		foreach ($click['type2'] as $key => $value) {
			$img_text[] = $value['new_id'];
		}
		$img_text = implode(',',$img_text);
		foreach ($click['type3'] as $key => $value) {
			$vodeos[] = $value['new_id'];
		}
		$vodeos = implode(',',$vodeos);
		$this->assign('text',$text);
		$this->assign('q',$get['q']);     
		$this->assign('img_text',$img_text);              
		$this->assign('vodeos',$vodeos);              
		$this->assign('data',$data);
		$this->display();
	}
	/**
	 * 创建点赞处理的方法
	 */
	public function chank_hlep(){
		if(IS_AJAX){
			$data = I('post.');
			if($data['type'] == 1){//文字
				$re = $this->media->hlepData($data);
			}else if($data['type'] == 2){//图文
				$re = $this->media->hlepData($data);
			}else if($data['type'] == 3){//视频
				$re = $this->media->hlepData($data);
			}
			$this->ajaxReturn($re);exit;
		}
	}
	/**
	 * 创建获取分页数据方法
	 */
	 	/**
		 * 1.获取页码；
		 * 2.获取对应数据；
		 */
	public function loading(){
	 	$arr = I('post.');
//	 	dump($arr);die;
	 	if($arr['type']){
	 		$data = false;
	 	}else{
			$data = $this->media->list_media($arr);
	 	}
		if(!$data){
			$this->ajaxReturn(0);exit;
		}else{
			$this->assign('data',$data);
			$content = $this->fetch();
			$this->ajaxReturn($content);exit;
		}
		
	 }
	/**
	 * 创建获取对应数据方法
	 * $type:传入类型
	 */
	public function get_right_data($type,$enum_type,$id){
		switch ($type) {
			case 1:
				$id_name = 'new_word_id';
				$table = 'new_article';
				break;
			case 2:
				$id_name = 'pic_text_id';
				$table = 'new_pic_text';
				break;
			case 3:
				$id_name = 'new_video_id';
				$table = 'new_video';
				break;
		}
		switch ($enum_type) {
			case '1':
				$key_name = 'bananer';//轮播
				break;
			case '2':
				$key_name = 'tiday';//头条
				break;
			case '3':
				$key_name = 'hot_spot';//热点
				break;
			case '4':
				$key_name = 'best_new';//最新
				break;
			case '5':
				$key_name = 'this_week';//本周
				break;
		}
		$data[$key_name] = M($table)->where(array($id_name=>$id))->find();
		return $data;
	}
	
	
}

?>