<?php
require_once("/var/www/lib/functions.php");
$uid=$_GET['uid'];
$offerId=$_GET['offerId'];
$refId=intval($_GET['refId']);
$dealtype=$_GET['otype'];
$title='';
$id=md5($uid.time());
$msg="";
$points=0;

if($dealtype=="DoneApp" || $dealtype=="App"){
 $install=db::row("select * from sponsored_app_installs where id=$refId and uid=$uid");
 if(!$install){
    $msg="Try this app first";
 }
 if($install['network']=="santa"){
	$prevUploadedByThisUser=db::row("select * from UploadPictures where refId=$refId and uid=$uid");
	if($prevUploadedByThisUser){
		$msg="You already uploaded a picture for this";
        }else{
         db::exec("update sponsored_app_installs set uploaded_picture=1 where id=$refId");
         db::exec("insert into UploadPictures set uid='$uid',refId='$refId',id='$id',offer_id='$offerId', type='SANTA',title='$title',created=now(), reviewed=0,points_earned=$points");
         error_log("insert into UploadPictures set uid='$uid',refId='$refId',id='$id',offer_id='$offerId', type='SANTA',title='$title',created=now(), reviewed=0,points_earned=$points");
       	$msg="Thanks for uploading this screenshot. It will be reviewed by our editorial staff. Points awarded will be based on the quality of the picture";
	}
 }
 else if($install['uploaded_picture']==0){
	 $points=$install['Amount'];
	if($uid<60000 && rand(0,10)<2){
//		$gem=1;
//		db::exec("update appuser set inviter_id =0, has_entered_bonus =0, banned=0 where id=$uid and has_entered_bonus=1 limit 1");
	}
	db::exec("update appuser set stars=stars+".$points." where id=$uid");
   	db::exec("update sponsored_app_installs set uploaded_picture=1 where id=$refId");
	$title=getRealIP();
	db::exec("insert into UploadPictures set uid='$uid',refId='$refId',id='$id',offer_id='$offerId', type='$dealtype',title='$title',created=now(), reviewed=0,points_earned=$points");
 }else{
    $msg="You already uploaded a picture for this offer";
 }
}else if ($dealtype=="contest"){
 $points=0;
///pr/uploadPicture.php?type=jpeg&offerId=38_586902335&otype=contest&uid=72402
 $offerId=$_GET['offerId'];
 $t=explode("_",$offerId);
 $contestId=$t[0];
 $storeId=$t[1];
 $contest=db::row("select * from contest where id=$contestId");
 $title=$storeId."";
 db::exec("insert into contest_entry set uid='$uid', pid='$id',created=now(),contest_id='$offerId',storeId=$storeId");
 db::exec("insert into UploadPictures set uid='$uid',refId='$offerId',id='$id',offer_id=$storeId, type='$dealtype',title='$title',created=now(), reviewed=0,points_earned=$points");
 echo trim($points."|".$id.".jpeg|http://www.json999.com/pr/postPicture.php|$msg|You have entered this contest! Please confirm your email address so we can deliver the prize gift code to this address");
 exit;
}
else if($dealtype=='UserOffers'){
 $prevUploadedByThisUser=db::row("select * from UploadPictures where refId=$refId and uid=$uid");
 if(!$prevUploadedByThisUser){
	$recent=db::row("select count(1) as cnt from UploadPictures where uid=$uid and created>date_sub(now(), interval 60 minute) and type='UserOffers'");
	if($recent['cnt']>3) die("0|".$id.".jpeg|http://www.json999.com/pr/postPicture.php|Please slow down and upload quality pictures. You can only upload 3 every hour!|");
	$recent=db::row("select count(1) as cnt from UploadPictures where uid=$uid and created>date_sub(now(), interval 24 hour) and type='UserOffers'");
	if($recent['cnt']>20) die("0|".$id.".jpeg|http://www.json999.com/pr/postPicture.php|Please slow down and upload quality pictures. You can only upload 20 a day.|");
	$offer=db::row("select * from PictureRequest where id=$refId");
	$points=$offer['cash_bid'];
 	$offeringUid=$offer['uid'];
	$title=$offer['title'];
	if($uid==$offeringUid) $points=0;
        $offeringUser=db::row("select * from appuser where id=$offeringUid");
	$status=1;
	$uploadCount=$offer['uploadCount'];
        $cap=$offer['max_cap'];
        if($offeringUser['stars']-$points<=0){
		db::exec("update PictureRequest set status=-1,cash_bid=0 where id=$refId limit 1");
		$msg="The seller ran out of money :(";	
		$points=0;		
	}else{
  	        if($offer['status']!=3){
			db::exec("update appuser set stars=stars-".$points." where id=$offeringUid");
			db::exec("update appuser set xp=xp+".($points*20)." where id=$offeringUid limit 1");
			if($uploadCount+1>=$cap){
         			       $status=-1;
        		}
		}else{
			$status=3;
		}
		db::exec("update appuser set stars=stars+".$points." where id=$uid limit 1");
	}
	db::exec("insert into UploadPictures set uid='$uid',refId='$refId',id='$id',type='$dealtype',title='$title',created=now(), reviewed=1,points_earned=$points");
	error_log("insert into UploadPictures set uid='$uid',refId='$refId',id='$id',type='$dealtype',title='$title',created=now(), reviewed=1,points_earned=$points");
	db::exec("update PictureRequest set uploadCount=uploadCount+1, status=$status where id=$refId");
	require_once("/var/www/html/pr/apns.php");
	if(rand(1,3)==2 && $uploadCount<$cap) apnsUser($offeringUid,"Someone post a picture to your title","");
  }else{
 	$points=0;
	$msg="You already uploaded a picture for this offer";
  }
}
if(!$points) $points=0;
if($points>0){
  $user=db::row("select * from appuser where id=$uid");
  checkBonusInviter($user);
}

$winmsg="You earned $points for this picture.";
if($gem==1){
  $winmsg.="\n You found a Gem!\nYou may enter another bonus code (pull to refresh)";
}
$ret=trim($points."|".$id.".jpeg|http://www.json999.com/pr/postPicture.php|$msg|$winmsg");
echo $ret;
error_log($ret);
?>
