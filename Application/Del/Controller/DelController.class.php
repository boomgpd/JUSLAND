<?php
namespace Del\Controller;
use Think\Controller;

//用户控制器
class DelController extends Controller {
	
	//删除图片
    public function index(){
    	//获得所有图片
    	$temp = $this->obtain();
    	$data = array();
    	foreach($temp as $k => $v){
    		if(!empty($v)){
    			$v = array_filter($v);
    			foreach($v as $kk => $vv){
    					$data[] = 'Uploads/'.$vv;
    			};
    		};
    	};
    	foreach(glob('Uploads/default/*') as $k => $v){
    		$data[] = $v;
    	};
    	$path = 'Uploads';
    	$this->del($path,$data);
    }

    //删除
    private function del($path,$data){
    	if(!is_dir($path)) return FALSE;
    	$types = array('jpg','jpeg','gif','png','bmp','tiff','psd','swf','svg','pcx','dxf','wmf','emf','lic','eps','tga');
    	foreach(glob($path.'/*') as $k => $v){
    		if(is_dir($v)){
    			$this->del($v,$data);
    		}else{
    			$num = strrpos($v,'.');
    			$type = strtolower(substr($v,$num+1));
    			if(in_array($type,$types)){
	    			if(!in_array($v,$data)){
	    				unlink($v);
	    			};
    			};
    		};
    	};
    }

    //获得图片名
    private function obtain(){
    	$temp = array();
    	$temp[] = M('Adver')->getField('pic_src',true);
    	$temp[] = M('Bananer')->getField('pic_src',true);
    	$temp[] = M('Bespoke')->getField('url',true);
    	$temp[] = M('Diy_marrier_message')->getField('logo',true);
    	$temp[] = M('Diy_marrier_message')->getField('portrait',true);
    	$showed = M('Diy_marrier_message')->getField('showed',true);
    	foreach($showed as $k => $v){
    		$temp[] = explode(',',$v);
    	};
    	$temp[] = M('Hotel')->getField('logo',true);
    	$temp[] = M('Member')->getField('headimg',true);
    	$temp[] = M('Merchant_case')->getField('img_url',true);
    	$temp[] = M('Merchant_combo')->getField('img_url',true);
    	$temp[] = M('Merchant_message')->getField('logo_fang',true);
    	$temp[] = M('Merchant_message')->getField('logo_yuan',true);
    	$temp[] = M('Merchant_message')->getField('logo_list',true);
    	$temp[] = M('Merchant_message')->getField('img_list',true);
    	$list_img = M('Merchant_remark')->getField('list_img',true);
    	foreach($list_img as $k => $v){
    		$temp[] = explode('|',$v);
    	};
    	$temp[] = M('New_article')->getField('top_big_bananer',true);
    	$temp[] = M('New_article')->getField('middel_small_bananer',true);
    	$temp[] = M('New_article')->getField('list_img',true);
    	$temp[] = M('New_index_list')->getField('img_src',true);
    	$temp[] = M('New_pic_text')->getField('list_img',true);
    	$temp[] = M('New_text_content')->getField('pic_src',true);
    	$temp[] = M('New_text_pic_index')->getField('img_src',true);
    	$temp[] = M('New_video')->getField('video_width_img',true);
    	$temp[] = M('New_video')->getField('video_height_img',true);
    	$temp[] = M('New_video')->getField('list_img',true);
    	$temp[] = M('News_index')->getField('img_src',true);
    	$temp[] = M('Photo')->getField('logo',true);
    	$temp[] = M('Picture')->getField('merchant_logo',true);
    	$url = M('Picture')->getField('url',true);
    	foreach($url as $k => $v){
    		$temp[] = explode('|',$v);
    	};
    	$temp[] = M('Picture_list')->getField('img_src',true);
    	$temp[] = M('Site_config')->getField('attr_value',true);
        $temp[] = M('Photo_category')->getField('clogo',true);
        $picture = M('Photo_product')->getField('picture',true);
        foreach($picture as $k => $v){
            $temp[] = explode('|',$v);
        };
    	return $temp;
    }
}