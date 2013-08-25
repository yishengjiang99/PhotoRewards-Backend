
<?php
require_once("/var/www/lib/functions.php");
$mac=$_GET['mac'];
$idfa=$_GET['idfa'];
$cb=$_GET['cb'];
$user=db::row("select * from appuser where app='$cb' and idfa='$idfa'");
$uid=$user['id'];
$refId=intval($_GET['refId']);
if($refId==1337){
  die(json_encode(array(array("id"=>"appstore","url"=>"http://i.imgur.com/wEq4jFGl.jpg","appstore"=>"1"))));
}
$storeId=$_GET['storeId'];
$type=$_GET['otype'];

if($type=="DoneApp"){
 $sql="select shared, title, a.id, type, liked, a.uid, points_earned,username, fbid from UploadPictures a left join appuser b on a.uid=b.id where offer_id='$storeId' and (reviewed=1 OR a.uid=$uid) order by a.uid=$uid desc, b.id desc";
}else if($type=="App" && $storeId!=""){
  $sql="select shared, title, a.id, type, liked, a.uid, points_earned,username, fbid from UploadPictures a left join appuser b on a.uid=b.id where offer_id='$storeId' and (reviewed>=1 OR a.uid=$uid) order by a.uid=$uid desc, b.id desc";
}else if($type=="UserOffers"){
  $sql="select shared, title, a.id, type, liked, a.uid, points_earned,username, fbid from UploadPictures a left join appuser b on a.uid=b.id where refId='$refId' and type='UserOffers' order by a.uid=$uid desc, a.created desc limit 10";
}else if($type=='MyUploads'){
  $sql="select shared, title, a.id, type, liked, a.uid, points_earned,username, fbid from UploadPictures a left join appuser b on a.uid=b.id where refId='$refId'";
}
echo $sql;
error_log($sql);
 $pics=db::rows($sql);
 $ret=array();
 foreach($pics as $pic){
	$ret[]=array("id"=>$pic['id'], "firstname"=>$pic['username'],"uid"=>$pic['uid'],"shared"=>0,"liked"=>0, "fbid"=>$pic['fbid'], "title"=>$pic['title'],'liked'=>$pic['liked'],'points_earned'=>(int)$pic['points_earned'],'url'=>'http://www.json999.com/pr/uploads/'.$pic['id'] . '.jpeg?t=1&uid='.$uid);
 }

if($_GET['otype']!="UserOffers" && $storeId!="(null)" && $storeId!="" && $storeId){
$meta=json_decode(file_get_contents("http://json999.com/appmeta.php?appid=$storeId"),1);
$screenshots=explode(",",$meta['screenshot']);
foreach($screenshots as $s){
 $ret[]=array("id"=>"appstore",'url'=>$s,'appstore'=>"1");
}
}

//if(count($ret)==0) $ret[]=array("id"=>"appstore","url"=>"http://i.imgur.com/wEq4jFGl.jpg","appstore"=>"1");
die(json_encode($ret));

