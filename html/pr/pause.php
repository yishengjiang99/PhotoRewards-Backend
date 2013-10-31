<?php
require_once("/var/www/lib/functions.php");

///pr/pause?id=&uid=2902&idfa=CDF4008D-BA7D-44B1-B623-3013091995AD&h=031a1828b97b0a600f79a65ba2d8de5d 
$idfa=$_GET['idfa'];
$mac=$_GET['mac'];
$uid=$_GET['uid'];
$h=md5($uid.$idfa."dfsssf");
if($h!=$_GET['h']){
 die();
}
$id=intval($_GET['id']);
if($_GET['cmd']=="Run"){
 db::exec("update PictureRequest set status=1,max_cap=max_cap+20 where id=$id limit 1");
}else{
 db::exec("update PictureRequest set status=-1 where id=$id limit 1");
}
header("location: picrewards://");
?>

