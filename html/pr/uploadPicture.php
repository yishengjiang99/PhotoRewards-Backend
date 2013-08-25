<?php
require_once("/var/www/lib/functions.php");

$uid=$_GET['uid'];
$offerId=$_GET['offerId'];
if(strpos($_GET['refId'],"_")!==FALSE){
 $t=explode("_",$_GET['refId']);
 $refId=intval($t[1]);
}else{
 $refId=intval($_GET['refId']);
}
$dealtype=$_GET['otype'];
$title='';
$id=md5($uid.time());
$msg="";
$points=0;
if($dealtype=="DoneApp" || $dealtype=="App"){
 $install=db::row("select * from sponsored_app_installs where id=$refId");
 if(!$install){
    $msg="Try this app first";
 }

 if($install['network']=="santa"){
	$prevUploadedByThisUser=db::row("select * from UploadPictures where refId=$refId and uid=$uid");
	if($prevUploadedByThisUser){
		$msg="You already uploaded a picture for this";
        }else{
         db::exec("update sponsored_app_installs set uploaded_picture=1 where id=$refId");
         db::exec("insert into UploadPictures set uid='$uid',refId='$refId',id='$id',offer_id='$offerId', type='$dealtype',title='$title',created=now(), reviewed=0,points_earned=$points");
       error_log("insert into UploadPictures set uid='$uid',refId='$refId',id='$id',offer_id='$offerId', type='$dealtype',title='$title',created=now(), reviewed=0,points_earned=$points");
  	$msg="Thanks for uploading this screenshot. It will be reviewed by our editorial staff for Points";
	}
 }
 else if($install['uploaded_picture']==0){
	 $points=$install['Amount'];
	 db::exec("update appuser set stars=stars+".$points." where id=$uid");
   	db::exec("update sponsored_app_installs set uploaded_picture=1 where id=$refId");
	db::exec("insert into UploadPictures set uid='$uid',refId='$refId',id='$id',offer_id='$offerId', type='$dealtype',title='$title',created=now(), reviewed=0,points_earned=$points");
 }else{
    $msg="You already uploaded a picture for this offer";
 }
}
else if($dealtype=='UserOffers'){
//die("");
 $prevUploadedByThisUser=db::row("select * from UploadPictures where refId=$refId and uid=$uid");
 if(!$prevUploadedByThisUser){
	$offer=db::row("select * from PictureRequest where id=$refId");
	$points=$offer['cash_bid'];
 	$offeringUid=$offer['uid'];
	$title=$offer['title'];
	if($uid==$offeringUid) $points=0;
	error_log(" $uid vs $offeringUid");
	
        $offeringUser=db::row("select * from appuser where id=$offeringUid");
//	error_log(json_encode($offer)." UPLOAD PICTURES from user ".json_encode($offeringUser));
        if($offeringUser['stars']-$points<=0){
		db::exec("update PictureRequest set status=-1,cash_bid=0 where id=$refId limit 1");
		$msg="The seller ran out of money :(";	
		$points=0;		
	}else{
  	        db::exec("update appuser set stars=stars-".$points." where id=$offeringUid");
		db::exec("update appuser set xp=xp+".($points*10)." where id=$offeringUid limit 1");
		db::exec("update appuser set stars=stars+".$points." where id=$uid limit 1");
	}
        $uploadCount=$offer['uploadCount'];
    	$cap=$offer['max_cap']; 
        $status=1;
        if($uploadCount+1>=$cap){
		$status=-1;
        }
	db::exec("insert into UploadPictures set uid='$uid',refId='$refId',id='$id',type='$dealtype',title='$title',created=now(), reviewed=1,points_earned=$points");
	db::exec("update PictureRequest set uploadCount=uploadCount+1, status=$status where id=$refId");
	require_once("/var/www/html/pr/apns.php");
	//if($offeringUid!=2902) apnsUser($offeringUid,"Someone post a picture to your title","Click Ok To see it!","http://json999.com/pr/p.php?pid=$id&uid=$offeringUid");
  }else{
 	$points=0;
	$msg="You already uploaded a picture for this offer";
  }
}
if(!$points) $points=0;
echo trim($points."|".$id.".jpeg|http://www.json999.com/pr/postPicture.php|$msg");
?>
