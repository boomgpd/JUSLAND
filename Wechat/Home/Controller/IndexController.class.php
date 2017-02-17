<?php
namespace Home\Controller;
use Think\Controller;
/**
 * 创建首页控制器
 * Author:boom
 * Date:2017/02/16
 */
class IndexController extends CommonController {
	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * 创建首页显示方法
	 */
    public function index(){
//  	echo '尊敬的用户'.$_SESSION['member_name'].'，欢迎来到嘉仕澜德';die;
		$this->display();
    }
   
}
