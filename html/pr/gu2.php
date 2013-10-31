<?php
require_once("/var/www/lib/functions.php");
$mac=$_GET['mac'];
$idfa=$_GET['idfa'];
$cb=$_GET['cb'];

$uid=intval($_GET['uid']);

$refId=intval($_GET['refId']);
//577
if($refId==577){
  die(json_encode(array(array("id"=>"appstore","url"=>"http://grepawk.s3.amazonaws.com/icon_invite_friends.png.jpeg","appstore"=>"1"))));
}
//sup.png
if($refId==888){
  die(json_encode(array(array("id"=>"appstore","url"=>"http://json999.com/img/sup.png","appstore"=>"1"))));
}
if($refId==991){
  //http://i.imgur.com/EApqc4n.png
   die(json_encode(array(array("id"=>"appstore","url"=>"http://i.imgur.com/EApqc4n.png?t=1&uid=$uid","appstore"=>"1"))));
}
if($refId==111){
  die(json_encode(array(array("id"=>"appstore","url"=>"http://grepawk.s3.amazonaws.com/pr2logo_blue.png","appstore"=>"1"))));
}
if($refId==889){
   die(json_encode(array(array("id"=>"appstore","url"=>"http://i.imgur.com/L2Qs1qh.png?t=1&uid=$uid","appstore"=>"1"))));
}
if($refId==993 || $refId==1000){
   die(json_encode(array()));
}
$storeId=$_GET['storeId'];
$type=$_GET['otype'];
if($type=="DoneApp" || $type=="CPA"){
 $sql="select compressed,shared, title, a.id, type, liked, a.uid, points_earned,username, fbid from UploadPictures a left join appuser b on a.uid=b.id where offer_id='$storeId' 
 and (reviewed=1 OR a.uid=$uid) and points_earned>0 order by a.uid=$uid desc, RAND() limit 15";
 if($uid==2902){
  $sql="select compressed,shared, title, a.id, type, liked, a.uid, points_earned,username, fbid from UploadPictures a left join appuser b on a.uid=b.id where offer_id='$storeId'
   and points_earned>0 order by a.uid=$uid desc, b.id desc limit 15";
 }
}else if($type=="SPA"){
 $appname=$_GET['refId'];
 $app=db::row("select * from apps where Name like '".urldecode($appname)."%'");

 if(!$app) {
 $url="http://json999.com/appmetaName.php?name=".$_GET['refId'];
   $app=json_decode(file_get_contents("http://json999.com/appmetaName.php?name=".$_GET['refId']),1);
 }
 if(!$app) die(json_encode(array()));
 $offerId=$_GET['storeId'];
 $storeId=$app['id'];
 $sql="select shared, title, a.id, type, liked, a.uid, points_earned,username,compressed, fbid from UploadPictures a left join appuser b on a.uid=b.id
 where offer_id='$storeId' and (reviewed>=1 OR a.uid=$uid) order by a.uid=$uid, a.created asc limit 10";  
 db::exec("insert ignore into extapps set appid=$storeId,offer_id='$offerId'");  
}
else if($type=="App" && $storeId!=""){
  $sql="select shared, title, a.id, type, liked, a.uid, points_earned,username,compressed, fbid from UploadPictures a left join appuser b on a.uid=b.id 
 where offer_id='$storeId' and (reviewed>=1 OR a.uid=$uid) order by a.uid=$uid, a.created asc limit 10";
}else if($type=="UserOffers"){
  $sql="select shared, title, a.id, type, liked, a.uid, points_earned,username, compressed,fbid from UploadPictures a left join appuser b on a.uid=b.id where refId='$refId' and type='UserOffers' and reviewed>=0 and points_earned>0 and banned<5 order by a.uid=$uid desc limit 15";
}else if($type=='MyUploads'){
  $sql="select shared, title, a.id, type, liked, a.uid, points_earned,username, compressed,fbid from UploadPictures a left join appuser b on a.uid=b.id where refId='$refId' and type='UserOffers' and banned<5 order by a.created desc";
}
if($sql){
 $pics=db::rows($sql);
 $ret=array();
 foreach($pics as $pic){
	$dir="";
       if($pic['compressed']==5){
         $dir="arch/";
       }
       $filepath="/var/www/html/pr/uploads/".$dir.$pic['id'] . ".jpeg";
       if(!file_exists($filepath)) continue;
	$ret[]=array("id"=>$pic['id'], "firstname"=>$pic['username'],"uid"=>$pic['uid'],"shared"=>0,"liked"=>0, "fbid"=>$pic['fbid'], 
        "title"=>$pic['title'],'liked'=>$pic['liked'],'points_earned'=>(int)$pic['points_earned'],'url'=>'http://www.json999.com/pr/uploads/'.$dir.$pic['id'] . '.jpeg?t=1&uid='.$uid);
 }
}

if(false && count($ret)<3 && $_GET['otype']!="UserOffers" && $storeId!="(null)" && $storeId!="" && $storeId){
 $meta=json_decode(file_get_contents("http://json999.com/appmeta.php?appid=$storeId"),1);
 $screenshots=explode(",",$meta['screenshot']);
 foreach($screenshots as $s){
   $ret[]=array("id"=>"appstore",'url'=>$s,'appstore'=>"1");
 }
}

//if(count($ret)==0) $ret[]=array("id"=>"appstore","url"=>"http://i.imgur.com/wEq4jFGl.jpg","appstore"=>"1");
die(json_encode($ret));

