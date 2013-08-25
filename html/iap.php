<?
require_once("/var/www/lib/functions.php");
$iaps=array("com.ragnus.picturerewards.6000points"=>6000,"com.ragnus.picturerewards.600points"=>600);
$_GET=$_REQUEST;
$idfa=$_GET['idfa'];
$mac=$_GET['mac'];
$cb=$_GET['cb'];
$iap=$_GET['id'];
$tid=$_GET['transId'];
$user=db::row("select * from appuser where app='$cb' and mac='$mac'");
$uid=$user['id'];
$extra="";
$sql="insert into iap (user_id,iap_id,created,expires,extra,transId) values ($uid, '$iap',now(), '$expires', '$extra',$tid)";
db::exec($sql);

$points=$iaps[$iap];
db::exec("update appuser set stars=stars+".$points." where id=$uid");
error_log("update appuser set stars=stars+".$points." where id=$uid");
error_log(json_encode($_REQUEST));
die(json_encode(array("title"=>"You win!","msg"=>"got $points points")));
