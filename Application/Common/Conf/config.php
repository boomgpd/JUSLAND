<?php
    return array (
  '__LOGO__' => '/Public/Home/logo.jpg',
  '__PAGE_NUM__' => '12',
  '__INFO_NUM__' => '300',
  'TMPL_PARSE_STRING' => 
  array (
    '__UPLOAD__' => '/Uploads/',
  ),
  '__MERCHANT_LOGO__' => 
  array (
    0 => 'default/3.jpg',
    1 => 'default/4.jpg',
    2 => 'default/5.jpg',
    3 => 'default/6.jpg',
  ),
  '__IP_AREA__' => 'http://ip.taobao.com/service/getIpInfo.php?ip=',
  '__THUMB__' => './Uploads/',
 
  /*邮箱配置*/
    'THINK_EMAIL' => array(

      'SMTP_HOST' => 'smtp.qq.com', //SMTP服务器
      
      'SMTP_PORT' => '465', //SMTP服务器端口
      
      'SMTP_USER' => '1316690605@qq.com', //SMTP服务器用户名
      
      'SMTP_PASS' => 'axlnsqdvigdeifhb', //SMTP服务器密码
      
      'FROM_EMAIL' => '1316690605@qq.com',
      
      'FROM_NAME' => '嘉士澜德-婚里人', //发件人名称
      
      'REPLY_EMAIL' => '', //回复EMAIL（留空则为发件人EMAIL）
      
      'REPLY_NAME' => '', //回复名称（留空则为发件人名称）
      
      'SESSION_EXPIRE'=>'72',
   ), 
  'TMPL_PARSE_STRING' => 
    array (
      '__UPLOAD__' => '/Uploads/',
    ),
    '__THUMB__' => './Uploads/',
  
  /*短信验证*/
  'APP_KEY'   =>  '23497551',
  'APP_SECRET'    =>  '54aaba2ca9311e3fff081c5bd40e7165',         
  );
?>
