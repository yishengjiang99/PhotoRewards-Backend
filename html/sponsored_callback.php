<?php
// $prcb="https://json999.com/sponsored_callback.php?mac=$mac&idfa=$idfa&storeID=$appid";

require_once("/var/www/lib/functions.php");
if($_GET['pw']=="dafhfadsfkdsadlds") $network='Yisheng';
else if($_GET['pw']=="dafhfadst444") $network='yunan';
else die(0);
$mac=$_GET['mac'];
$idfa=$_GET['idfa'];
$appid=intval($_GET['storeID']);
$offer=db::row("select payout,cash_value,id,active from offers where storeID=$appid");
if($offer['active']==0){
 exit;
}
else if($offer['active']>=20){
  $channel="contest";
}else{
  $channel="picrewards";
}
$user=db::row("select * from appuser where idfa='$idfa' and app='$channel' and banned=0 and active=1 order by id asc limit 1");
if(!$user) die("0");
$uid=$user['id'];
if($installed=db::row("select * from sponsored_app_installs where uid=$uid and appid=$appid")){
 exit;
}
db::exec("update appuser set source='$channel' where id=$uid");
$points=intval($offer['cash_value'])*10;
$offerId=$offer['id'];
$subid=$uid."_".$offerId;

$rev=$offer['payout'];
$transactionID=$user['id'];
db::exec("insert ignore into sponsored_app_installs set uid=$uid, Amount=$points,transactionID='$transactionID', appid=$appid, created=now(),offer_id=$offerId, subid='$subid',network='$network',revenue=$rev");
$installid=db::lastID();
$appstr=file_get_contents("http://json999.com/appmeta.php?appid=$appid");
$app=json_decode($appstr,1);
$appname=$app['Name'];
error_log($appname);
$message="Thanks for trying $appname! Share a screenshot on Picture Rewards!";
require_once("/var/www/html/pr/apns.php");
apnsUser($uid,$message,"");
//apnsUser(2902,$message);
exit;
