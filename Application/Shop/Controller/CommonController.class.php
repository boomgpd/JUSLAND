<?php
namespace Shop\Controller;
use Think\Controller;
use Home\Model\MemberModel;
use Common\Model\MenuModel;
use Common\Model\ConfigModel;

class CommonController extends Controller {
	protected $member;
	protected $config;
//	城市首字母种子
	protected $seek = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
	
	public function __construct(){
		parent::__construct();
		$this-> member   	= new MemberModel;
		$this-> config     	= new ConfigModel;
		$this->header_data();
		$this->header_two();
		$this->bottom();
		$this->right();
		// $this->default_city();
	}
	
	/**
	 * 右侧购物车
	 */
	public function right(){
		$cartss = M('shop_goods_cart')->where(array('memder_id'=>$_SESSION['memder_id'],'is_del'=>0))->count();
		$this->assign('cartss',$cartss);
	}
	 public function code(){
        $config=array (
                'fontSize'    =>    25,    // 验证码字体大小    
                'length'      =>    4,     // 验证码位数   
                'useNoise'    =>    true, // 关闭验证码杂点
                'imageW'      =>    180,   //图片宽度
                'imageH'      =>    50,    //图片高度
//              'useImgBg'	  => 	true,  //背景图片
                'useCurve'	  =>	true   //混淆曲线
            );
        $Verify = new \Think\Verify($config);
		$img = $Verify->entry();
//		dump($_SESSION);
        return $img;//返回图片 
    }
	 
	 /**
	  * 创建公共头部数据方法
	  */
	 public function header_data(){
		$controller = CONTROLLER_NAME;
		$this->assign('controller',$controller);
	 	if($_SESSION['member_id']){
	 		$header_data['member'] = $this->member->getData();
	 	}else{
	 		$header_data['member'] = 0;
	 	}
		$header_data['phone'] = M('site_config')->where(array('attr_name'=>'咨询电话'))->getField('attr_value');
    	$header_data['logo'] = M('site_config')->where(array('attr_name'=>'logo'))->getField('attr_value');
    	$this->assign('header_data',$header_data);
	 }
	 /**
	  * 创建二级公共头部的方法
	  */
	 public function header_two(){
	 	$header_two['phone'] = M('site_config')->where(array('attr_name'=>'咨询电话'))->getField('attr_value');
	 	$member = M('member')->where(array('member_id'=>$_SESSION['member_id']))->find();
	 	$this->assign('member',$member);
	 	$this->assign('header_two',$header_two);
	 }
	 /**
	  * 创建公共底部显示的方法
	  */
	 public function bottom(){
	 	$bottom = M('sys')->where(array('is_del'=>0,'pid'=>0))->select();
	 	$this->assign('bottom',$bottom);
	 }
	 /**
	  * 创建获取商家城市
	  */
	 public function getCity(){
	 	if(!IS_AJAX) return false;
	 	$data = M('merchant')->field('city,provience')->select();
	 	$data = array_unique($data);
	 	//获取直辖市主键
	 	$special_city = M('area')->where(array('aname'=>array('in','北京市,天津市,重庆市,上海市')))->getField('area_id',true);
//	 	获取城市对应的首字母和名称
	 	$cityData = array();
	 	foreach($data as $k=>&$v){
	 		if(in_array($v['provience'],$special_city)){
	 			$id = $v['provience'];
	 		}else{
	 			$id = $v['city'];
	 		}
	 		$name = M('area')->where(array('area_id'=>$id))->getField('aname');
	 		$first_char = $this->_getFirstCharter($name);
	 		$arr =  array('area_id'=>$id,'name'=>$name,'first_char'=>$first_char);
	 		$cityData[] = $arr;
	 	}
//		dump($cityData);die;
//	 	获取返出的数据格式
	 	$ajax_data = array();
	 	$seek = $this->seek;
	 	foreach($seek as $k=>$v){
	 		foreach($cityData as $key=>$value){
	 			if($value['first_char'] == $v){
	 				$ajax_data[$k]['first_chat'] = $v;
	 				$ajax_data[$k]['data'][] = $value;
	 			}
	 		}
	 	}
//	 	dump($ajax_data);die;
//	 	return $cityData;
		$this->ajaxReturn($ajax_data);
	 }
	 
	 
	 /**
	  * 创建获取汉字首字母方法
	  */
	public function _getFirstCharter($str){
		if(empty($str)){return '';}
		$fchar=ord($str{0});
		if($fchar>=ord('A')&&$fchar<=ord('z')) return strtoupper($str{0});
		$s1=iconv('UTF-8','gb2312',$str);
		$s2=iconv('gb2312','UTF-8',$s1);
		$s=$s2==$str?$s1:$str;
		$asc=ord($s{0})*256+ord($s{1})-65536;
		if($asc>=-20319&&$asc<=-20284) return 'A';
		if($asc>=-20283&&$asc<=-19776) return 'B';
		if($asc>=-19775&&$asc<=-19219) return 'C';
		if($asc>=-19218&&$asc<=-18711) return 'D';
		if($asc>=-18710&&$asc<=-18527) return 'E';
		if($asc>=-18526&&$asc<=-18240) return 'F';
		if($asc>=-18239&&$asc<=-17923) return 'G';
		if($asc>=-17922&&$asc<=-17418) return 'H';
		if($asc>=-17417&&$asc<=-16475) return 'J';
		if($asc>=-16474&&$asc<=-16213) return 'K';
		if($asc>=-16212&&$asc<=-15641) return 'L';
		if($asc>=-15640&&$asc<=-15166) return 'M';
		if($asc>=-15165&&$asc<=-14923) return 'N';
		if($asc>=-14922&&$asc<=-14915) return 'O';
		if($asc>=-14914&&$asc<=-14631) return 'P';
		if($asc>=-14630&&$asc<=-14150) return 'Q';
		if($asc>=-14149&&$asc<=-14091) return 'R';
		if($asc>=-14090&&$asc<=-13319) return 'S';
		if($asc>=-13318&&$asc<=-12839) return 'T';
		if($asc>=-12838&&$asc<=-12557) return 'W';
		if($asc>=-12556&&$asc<=-11848) return 'X';
		if($asc>=-11847&&$asc<=-11056) return 'Y';
		if($asc>=-11055&&$asc<=-10247) return 'Z';
		return null;
    }
	 /**
	  * 创建调用弹窗的方法
	  */
	public function alert_view($data){
		$data = json_encode($data);
		echo '<script type="text/javascript">alert_view('.$data.');</script>';
	}
	/**
	 * 创建上传图片类
	 */
	 public function upload(){
	 	$name = I('get.name');
	 	$upload = new \Think\Upload();// 实例化上传类
	    $upload->maxSize   =     3145728 ;// 设置附件上传大小
	    $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
	    $upload->rootPath  =     __ROOT__; // 设置附件上传根目录
	    $upload->savePath  =     $name.'/'; // 设置附件上传（子）目录
	    // 上传文件 
	    $info   =   $upload->upload();
	    if (empty($info)) {
	        echo json_encode($this->error());exit;
	    } else {
	        //上传成功，把上传好的信息返给js
	        $data = $info['Filedata']['savepath'].$info['Filedata']['savename'];
	        echo json_encode($data);exit;
	    }
	 }
	 
	 
	 /**
	  * 创建切换登录城市方法
	  */
	 public function change_location_city(){
	 	if(!IS_AJAX) return false;
	 	$area_id = I('post.area_id');
	 	$_SESSION['location_city_id'] = $area_id;
	 	$this->ajaxReturn($area_id);
	 }
	 
	 /**
	  * 创建获取ip方法
	  */
	 public function getIP(){
        static $realip;
        if (isset($_SERVER)){
            if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
                $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
            } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
                $realip = $_SERVER["HTTP_CLIENT_IP"];
            } else {
                $realip = $_SERVER["REMOTE_ADDR"];
            }
        } else {
            if (getenv("HTTP_X_FORWARDED_FOR")){
                $realip = getenv("HTTP_X_FORWARDED_FOR");
            } else if (getenv("HTTP_CLIENT_IP")) {
                $realip = getenv("HTTP_CLIENT_IP");
            } else {
                $realip = getenv("REMOTE_ADDR");
            }
        }
        return $realip;
    }
	 
	/**
	 * 创建获取默认地址
	 */
	 public function default_city(){
	 	$ip = $this->getIP();
		$str = C('__IP_AREA__').$ip;
		$obj = json_decode(file_get_contents($str));
		$arr = array();
		$arr[] = $obj->data->city;
		$arr[] = $obj->data->county;
		$_SESSION['location_city_name'] = $obj->data->city;
		$_SESSION['location_city_id'] = M('area')->where(array('anama'=>$_SESSION['location_city_name']))->getField('area_id');
	 }
//	 /**
//	  * 跨模块跳转
//	  * $content 提示内容
//	  * $url 跳转路径
//	  */
//	public function php_alert($content=NULL,$url=NULL){
////		if(!empty($content) && !empty($content)) return FALSE;
//		$alert = array(
//			'content'=>$content,
//			'url'=>$url,
//			'type'=>1
//		);
//		return $alert;
//	} 
	 
}