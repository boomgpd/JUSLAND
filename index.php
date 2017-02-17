<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// 应用入口文件
// 检测PHP环境
header('Content-type:text/html;charset=utf-8');
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

//判定是否是微信端登录
// 定义应用目录
if(strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ){
    define('APP_PATH','./Wechat/');
}else{
	define('APP_PATH','./Application/');
}  
        

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',True);

// 引入ThinkPHP入口文件
require './ThinkPHP/ThinkPHP.php';


