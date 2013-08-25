<?
require_once("/var/www/lib/functions.php");
$ua=$_SERVER['HTTP_USER_AGENT'];
$reviewer=0;
if(strpos($ua,"PictureRewards/1.1")!==false){

 $reviewer=1;
}


$uid=intval($_GET['uid']);
$refstr=$_GET['refId'];
$appId=intval($refstr);
$ua=$_SERVER['HTTP_USER_AGENT'];
if(strpos($ua,"PictureRewards/1.2")!==false){
   db::exec("insert ignore into sponsored_app_installs set uid=$uid, appid=$appId,Amount=0, created=now(),revenue=0, network='santa', uploaded_picture=1");
   $installId=db::lastID();
   die($installId);
}


$install=db::row("select * from sponsored_app_installs where uid=$uid and appid=$appId");
if($install) {
 die($install['id']."");
}
else {
 die("no");
}

