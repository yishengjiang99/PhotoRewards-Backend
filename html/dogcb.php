<?php
require_once("/var/www/lib/functions.php");
if($_GET['pw']!="dafhfadsfkdsadlds") die(0);
if(getRealIP()!="174.36.32.234"){
 error_log("BAD IP CB");
 die("erro");
}
$appid=intval($_GET['appid']);
$uid=intval($_GET['uid']);
$user=db::row("select * from appuser where id=$uid");
$ltv=$user['ltv'];
$dogmulti=300;
if($ltv<100) $dogmulti*500;
$points=$_GET['payout']*$dogmulti;
$points=min(300,$points);
$revenue=doubleval($_GET['payout'])*100;
$subid="";
$rev=$revenue;
$offerID=$appid;
$transID=$uid."_".$appid;

db::exec("insert ignore into sponsored_app_installs set uid=$uid, Amount=$points, revenue='$revenue', appid=$appid, network='appdog', subid='$uid_$appid',transactionID='$transID', offer_id=$appid, created=now()");
db::exec("update appuser set ltv=ltv+$revenue where id=$uid");
$user=db::row("select * from appuser where id=$uid");
checkBonusInviter($user);
$appstr=file_get_contents("http://json999.com/appmeta.php?appid=$appid");
$app=json_decode($appstr,1);
$appname=$app['Name'];
$appname=substr($appname,0,40);
$message="Thanks for trying $appname! Share a screenshot on Picture Rewards!";
$msg="Thanks for trying $appname. Upload a picture for $points points";
require_once("/var/www/html/pr/apns.php");
apnsUser($uid,$msg,$msg);

