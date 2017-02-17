<?php namespace Merchant\Controller;
use Think\Controller;
/**
 * 商家管理公共控制器
 * boom
 * 08/12
 */
class CommonController extends Controller {
	
	public function __construct(){
		parent::__construct();
		if(!$_SESSION['merchant_id']){
			redirect(U('home/index/index'));
		}
	}
	

}