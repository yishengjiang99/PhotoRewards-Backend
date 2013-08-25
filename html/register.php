<?php
require_once("/var/www/lib/functions.php");

$_POST=$_REQUEST;

$email=$_POST['email'];
$pword=md5($_POST['password']);
$mac=$_POST['mac'];
$app=$_POST['cb'];

$register=0;
if(isset($_POST['register'])){
 $register=intval($_POST['register']);
}

$account=db::row("select * from app_accounts where email='$email' and app='$app'");
$user=db::row("select * from appuser where mac='$mac' and app='$app'");
if(!$user){
 die("device not registered...crash app!");
}
$uid=$user['id'];

if($account){
 if($account['password']==$pword){
   db::exec("update appuser set email='$email' where id=$uid");
   $merged_iap=merge_iap($email,$app,$user['id']);
   
   $ret=array(
    "email"=>$email,
    "msg"=>"Welcome back $email. $merged_iap",
    );
 }else{
    $ret=array("error"=>"You already registered with this email but the password we have do not match. Please email yisheng.jiang@gmail.com if you believe this is an error");
  }

}else{
  if($register==1){
    db::exec("insert ignore into app_accounts set email='$email',password='$pword',app='$app',created=now()");
    db::exec("update appuser set email='$email' where id=$uid");
    $ret=array(
     "email"=>$email, 
     "msg"=>"Thank you for registering your email $email. You may restore subscriptions on your other devices"
    );
  }else{
    $ret=array("error"=>"Email is not found");
  }
}

die(json_encode($ret));

function merge_iap($email,$app,$userid){
 $select="select i.* from iap i join appuser a on i.user_id=a.id where a.email='$email' and a.id!=$userid and i.merged=0";
 $iaps=db::rows($select);
 foreach($iaps as $iap){
  $iap['user_id']=$userid;
  $iap_id=$iap['iap_id']; $created=$iap['created']; $extra=$iap['extra']; $merged=1; $transId=$iap['transId'];
  $expires=$iap['expires'];
  $insert="insert ignore into iap set iap_id='$iap_id',created='$created',extra='$extra',merged=1,transId='$transId',expires='$expires',user_id=$userid";
  db::exec($insert);
 }
 if(count($iaps)>0){
  return "Your previously purchased subscriptions has been merged onto this device";
 }else{
  return "";
 }
}
