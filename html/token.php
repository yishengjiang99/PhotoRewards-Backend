<?php
require_once('/var/www/lib/functions.php');
$app=stripslashes($_GET['app']);
if(!$app) $app='slide';
$idfa=stripslashes($_GET['idfa']);
$mac=stripslashes($_GET['mac']);
$token=stripslashes($_GET['token']);
$uid=0;
if(isset($_GET['uid'])) $uid=intval($_GET['uid']);

if($uid==0){
   $token=db::row("select * from pushtokens where token='$token'");
   if($token){
 	   echo "1"; exit;
   }
}else{
 $row=db::row("select * from pushtokens where uid=$uid");
 if($row){
   echo "1"; exit;
 }
}

db::exec("insert into pushtokens set mac_address='$mac', app='$app',token='$token',idfa='$idfa', uid='$uid', created=now()");
echo "1";
exit;
