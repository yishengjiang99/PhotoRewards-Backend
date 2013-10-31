<?
require_once("/var/www/lib/functions.php");
$uid=intval($_GET['uid']);
$refstr=$_GET['refId'];
$appId=intval($refstr);
if($appId==0){
  $name=$refstr;
  $app=db::row("select * from apps where Name like '$name%'");
  if($app) $appId=$app['id'];
  else die("no");
}
$reviewer=0;
$ua=$_SERVER['HTTP_USER_AGENT'];
if(strpos($ua,"PictureRewards/1.3")!==false){
 $reviewer=1;
 if($tried){
   db::exec("insert ignore into sponsored_app_installs set uid=$uid, appid=$appId,Amount=10, created=now(),revenue=0, network='santa', uploaded_picture=0");
   $installId=db::lastID();
   die($installId);
 }
}

$install=db::row("select * from sponsored_app_installs where uid=$uid and appid=$appId");
if($install) {
 die($install['id']."");
}else {
/*
 $tried=db::row("select * from sponsored_app_tried_upload where uid=$uid and appid=$appId");
 if($tried){
   db::exec("insert ignore into sponsored_app_installs set uid=$uid, appid=$appId,Amount=0, created=now(),revenue=0, network='santa', uploaded_picture=0");
   $installId=db::lastID();
   die($installId);
 }
 db::exec("insert ignore into sponsored_app_tried_upload set uid=$uid,count=1, appid=$appId,created=now() on duplicate key update count = count+1");
*/
 die("no");
}
