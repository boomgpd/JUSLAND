<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<title>JUSLAND</title>
</head>
<body>
	<p style="font-size: 40px;">尊敬的用户<?php echo ($_SESSION['member_name']); ?>,欢迎来到嘉仕澜德婚庆一站式服务平台;</p>
	<img src="<?php echo ($_SESSION['headimg']); ?>" style="width: 200px;height: 200px;"/>
</body>
</html>