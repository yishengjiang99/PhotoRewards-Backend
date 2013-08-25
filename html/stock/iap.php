<?php
require_once('/var/www/lib/functions.php');
$mac=$_GET['mac'];
$cb=$_GET['cb'];
$iap=$_GET['id'];
$tid=$_GET['transId'];
$date=intval($_GET['date']);

$user=db::row("select * from appuser where mac='$mac'");
$uid=$user['id'];

$existing=db::row("select * from iap where transId=$tid and user_id=$uid");
if($existing){
 error_log("existing transaction..");
 die("0");
}
$msg="";
if($iap=="com.ragnus.stockalerts.12month"){
	$expires=date("Y-m-d",$date+366*60*60*24);
	$msg="You are now a VIP member and can track up to 20 stocks with Instant Notification.";
}else if($iap=="com.ragnus.stockalerts.threemonth"){
	$expires=date("Y-m-d",$date+95*60*60*24);
	$extra=20;
        $msg="You are now a VIP	member and can track up	to 20 stocks with Instant Notification";
        if($expires<time()){
	    die("0");
        }
}else if($iap=="com.ragnus.stockalerts.instant"){
	$expires=date("Y-m-d",$date+10000000000);
	$extra=0;
        $msg="You are now a PRO member and can track stocks with 1-minute frequency";
}else{
 die("0");
}
        $extra=20;
$datestr=date("Y-m-d",$date);
$sql="insert into iap (user_id,iap_id,created,expires,extra,transId) values ($uid, '$iap','$datestr', '$expires', '$extra',$tid)";
db::exec($sql);
if($user['email']==""){
 $msg.="\n\rBe sure to register your email from the Settings tap so you can restore your subscriptions from other devices";
}
die($msg);
?>


