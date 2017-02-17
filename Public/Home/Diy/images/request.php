<?php
header('Content-type:text/html charset=gb2312');
$sql = mysql_connect("localhost","root","root") or die('error1');
mysql_select_db("waterfall",$sql) or die ('error2');
mysql_query("set names utf8",$sql);
$result = mysql_query("select * from demo;",$sql);
$data = array();
while($row = mysql_fetch_assoc($result)){
	$data[] = $row;	
}
sleep(3);
echo json_encode($data);
//$data = json_encode($data);
//$callback = $_GET['callback'];
//echo $callback.'('.json_encode($data).')';
//exit;
?>