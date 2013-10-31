<?php
require_once("/var/www/lib/functions.php");
$req=array();
foreach($_REQUEST as $k=>$v){
 $req[$k]=mysql_real_escape_string($v);
}
$_GET=$req;
$mac=$_GET['mac'];
$idfa=$_GET['idfa'];
$cb=$_GET['cb'];
error_log(json_encode($req));
$user=db::row("select * from appuser where app='$cb' and idfa='$idfa'");
$uid=$user['id'];

if(!isset($_GET['cmd']) || $_GET['cmd']!="moreInfo"){
  $title=$_GET['title'];
  $bid=$_GET['bid'];
  if($title=='(null)'){
     die(json_encode(array('title'=>'pwned','msg'=>'title cannot be null')));
 }
  if($bid>50) die(json_encode(array("title"=>"Not Saved","msg"=>"Something wrong")));
  $quand=$_GET['q']; 
  $category=$_GET['category'];
  $status=-1;
  if($user['stars']<=$bid){ 
     die(json_encode(array('title'=>'pwned','msg'=>'You have zero points! Upload some pictures to earn some points first!')));
  }
  else {
    $append="Would you like to enter additional information about this listing?";
    $status=0;
  }
  $lastpost=db::row("select * from PictureRequest where uid=$uid and created>date_sub(now(), interval 2 hour) limit 1");
  if($uid!=2902 && $lastpost){
     die(json_encode(array('title'=>'pwned','msg'=>'You can only post one request every 10 hours')));
  }
  $status=0;
  if($uid==2902) $status=3;
  $sql="insert into PictureRequest set uid=$uid,title='$title',cash_bid=$bid,max_cap=$quand,category='$category',status=$status,created=now()";
  db::exec($sql);
  $rid=db::lastID();
  if($rid) $ret=array("title"=>"Saved","msg"=>"Your request for '$title' pictures has been saved and will be reviewed. Would you like to enter additional information about this listing?","offerId"=>$rid);
  else $ret=array("title"=>"Not Saved","msg"=>"Something wrong");
  die(json_encode($ret));
}
else{
 $url=$_GET['url'];
 $offerId=$_GET['offerId'];
 $description=$_GET['description'];
 db::exec("update PictureRequest set description='$description', url='$url' where id=$offerId");
 $sms=$_GET['sms'];
 $email=$_GET['email'];
 if($sms && $sms!=''){
   db::exec("update appuser set sms='$sms' where id=$uid limit 1");
 } 
 $ret=array("title"=>"All Done","msg"=>"We will review this listing ASAP.");
}

die(json_encode($ret));
