<?php
require_once("/var/www/lib/functions.php");
//require_once("/var/www/lib/asynresponse.php");

$uid=intval($_GET['uid']);
$ip=getRealIP();
if($uid==73790) die("0");
$vote=intval($_GET['vote']);
$pid=$_GET['pid'];
if($pid=="appstore"){
 die("0");
}
$voted=db::row("select * from contest_votes where uid=$uid and pid='$pid'");

if($voted){
 die("0");
}

$user=db::row("select * from appuser where id=$uid");
$entry=db::row("select * from contest_entry where pid='$pid'");
$picture=db::row("select * from UploadPictures where id='$pid'");

if($picture['uid']==$uid){
 die("1");
}
$storeId=$picture['offer_id'];
$uploader=$picture['uid'];
$contestId=0;

if($entry){
  $contestId=$entry['contest_id'];
}

if(!$entry){
  
  error_log("insert into contest_entry set uid=$uploader, pid='$pid',created=now(),contest_id='$contestId',storeId=$storeId");
  db::exec("insert into contest_entry set uid=$uploader, pid='$pid',created=now(),contest_id='$contestId',storeId=$storeId");
}
 db::exec("insert into contest_votes set uid=$uid,vote=$vote,pid='$pid',created=now(),contest_id=$contestId,ip='$ip'");
 if($vote>1){
   db::exec("update contest_entry set points=points+$vote where pid='$pid'");
 }
 if($vote==1 && ($uid==83864 || $uid==72402)){
   db::exec("update UploadPictures set reviewed=-1 where id='$pid'");
   error_log("update UploadPictures set reviewed=-1 where id='$pid'");
 }
  $username=$user['username'];
  $uploader=$picture['uid'];
  $title=$picture['title'];
  if($storeId){
     $app=db::row("select * from apps where id=".$picture['offer_id']);
     $title=$app['Name'];
   }
  $msg="$username just rated your picture $vote Stars on Photo Contest!";
  $link="http://bit.ly/1cLCdIy";
  db::exec("update UploadPictures set shared=shared+1 where id='$pid'");
  require_once("/var/www/html/pr/apns.php");
  apnsUser($uploader,$msg,$msg,$link);
echo "1";
exit;
