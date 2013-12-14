<?php
///contest/canupload.php?uid=72402&offerId=32_589653414&mac=ios7device&cb=contest&idfa=A8087286-CA9D-4E96-AD3A-314F34D1E86B&t=1386035579&h=5ce4044c0f18ea570a739428469b27c0
require_once("/var/www/lib/functions.php");

$tt=explode("_",$_GET['offerId']);
$contestId=$tt[0];
$appId=$tt[1];
$uid=intval($_GET['uid']);
$install=db::row("select * from sponsored_app_installs where uid=$uid and appid=$appId");
if(!$install){
  $app=db::row("select * from apps where id=$appId"); 
  $name=$app['Name'];
  $subid=$uid."_".$appId;
//  $sql="insert into contest_clicks set subid='$uid', uid=$uid, offer_id='$appId',created=now(), url=''";  
  db::exec($sql);
  die(json_encode(array("cu"=>"no","msg"=>"Please try $name and take a screenshot from within the app to enter this contest. Click 'OK' to download")));
}
$existingEntry=db::row("select * from contest_entry where uid=$uid and storeId=$appId");
if($existingEntry){
 die(json_encode(array("cu"=>"no","msg"=>"You already entered this contest!")));
}
die(json_encode(array("cu"=>"yes")));
