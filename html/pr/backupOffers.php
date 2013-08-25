<?php
require_once("/var/www/lib/functions.php");
require_once("/var/www/html/pr/levels.php");
$idfa=$_GET['idfa'];
$mac=$_GET['mac'];
$uid=$_GET['uid'];

$start=intval($_GET['start']);
if(!$start) $start=0;
$o=array();
if($start==0){
 $sql="select 'DoneApp' as OfferType, 'Upload a SnapShot' as Action, s.id as refId, a.id as StoreID, a.Name, a.IconURL,s.amount as Amount, 1 as canUpload from sponsored_app_installs s join apps a on s.appid=a.id where uid=$uid and uploaded_picture=0";
 $o=array_merge($o,db::rows($sql));
}


$url="http://api.appdog.com/offerwall?limit=10&offset=$start&type=json&source=9135311512939222220&idfa=$idfa&fbid=$uid&mac=$mac";
error_log($url);
$offers=json_decode(file_get_contents($url),1);
foreach($offers as $offer){
 if($offer['OfferType']!="App") continue;
 if($offer['Cost']!="Free") continue;
 $points=$offer['Amount']*10;
 $offer['OfferType']="App";
 $offer['Name'] = str_ireplace("download ","",$offer['Name']);
 $offer['Amount']=$points."";
 $offer['hint']="Download";
 $offer['refId']=$offer['StoreID'];
 $offer['canUpload']=0;
 $offer["StoreID"]=$offer['StoreID'];
 $offer["Action"]="(FREE) Upload a Snapshot";
 $o[]=$offer;
}

$sql="select 'UserOffers' as OfferType, 'Take a picture' as Action, id as refId,'localt' as IconURL, title as Name, url as RedirectURL, category, cash_bid as Amount, 1 as canUpload from PictureRequest where status=0 order by created>date_sub(now(), interval 5 hour), cash_bid desc limit ".$start.", 10";
$o=array_merge($o,db::rows($sql));

$user=db::row("select * from appuser where id=$uid");
$xpinfo=getBonusPoints($user['xp']);

$canEnterBonus = $user['has_entered_bonus'] == 1 ? 0 : 1;

//uncomment this when app's under review
/*
$xpinfo['minbonus']=0;
$canEnterBonus=0;
*/

$ret=array(
"offers"=>$o,
"fb"=>0,
"invite"=>$xpinfo['minbonus'],
"inviteUpper"=>$xpinfo['maxbonus'],
"enterbonus"=>$canEnterBonus,
"st"=>1,
);
die(json_encode($ret));
