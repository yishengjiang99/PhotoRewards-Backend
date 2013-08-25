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
$tid=time();
$datestr=date("Y-m-d",$date);
$sql="insert into iap (user_id,iap_id,created,expires,extra,app,transId) values ($uid, '$iap',now(), '$expires', '$extra','slide',$tid)";
echo $sql;
db::exec($sql);
die($msg);
?>


