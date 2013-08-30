<?
require_once("/var/www/lib/functions.php");
$ua=$_SERVER['HTTP_USER_AGENT'];
$reviewer=0;
if(strpos($ua,"PictureRewards/1.2")!==false){
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
 $e="canupload_yes";
 if(time() % 1==0) db::exec("insert into app_event set t=now(), name='$e', m=1");
 die($install['id']."");
}
else {
 $e="canupload_no";
 if(file_exists("/var/www/cache/tried$uid$appId")){
   if(!file_exists("/var/www/cache/remind$uid") || rand(0,4)==2){
    require_once("/var/www/html/pr/apns.php");
    apnsUser($uid,"Please try some other apps if you've downloaded this one","It can take up to 2 hours for the advertiser to confirm that you tried the app.\n\nPlease wait a bit and try some other apps");
    touch("/var/www/cache/remind$uid");
   }
 }else{
  touch("/var/www/cache/tried$uid$appId"); 
 }
 if(time() % 5==0) db::exec("insert into app_event set t=now(), name='$e', m=5");
 die("no");
}

