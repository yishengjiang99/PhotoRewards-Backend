<?php
require_once("/var/www/lib/functions.php");
$uid=intval($_GET['uid']);
$recent=db::cols("select count(1) as cnt from pr_xp where uid=$uid and created>date_sub(now(), interval 10 minute)");
if($recent[0]>5) exit;
$xp=rand(2,5);
$e=stripslashes($_GET['e']);
db::exec("update appuser set xp = xp+$xp where id=$uid");
db::exec("insert into pr_xp set uid=$uid,xp=$xp,created=now(),event='$e'");
$uid=intval($_GET['uid']);
if($_GET['e']=="tweeted_picture" || $e=='liked_picture'){
  $user=db::row("select username from appuser where id=$uid");
  $username=$user['username'];
  $pid=$_GET['refId'];
  $picture=db::row("select * from UploadPictures where id='$pid'");
  $uploader=$picture['uid'];
  $title=$picture['title'];
  if($picture['type']="DoneApp"){
     $app=db::row("select * from apps where id=".$picture['offer_id']);
     $title=$app['Name'];
   }
  $msg="$username just shared your picture about $title for $xp xp";
  error_log($msg);
  db::exec("update UploadPictures set shared=shared+1 where id='$pid'");
  require_once("/var/www/html/pr/apns.php");
  apnsUser($uploader,$msg,$msg);  
}
