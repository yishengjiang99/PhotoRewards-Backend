<?php
require_once("/var/www/lib/functions.php");
$idfa=$_GET['idfa'];
$mac=$_GET['mac'];
$uid=$_GET['uid'];
$o=array();

//$o[]=array("Name"=>"FB Login","Amount"=>"100","OfferType"=>"fblogin", "Action"=>"Log into Facebook for 100 points");

$sql="select uploadCount, 'localt' as IconURL,'MyUploads' as OfferType, status, id as refId, title as Name, url as RedirectURL, category, cash_bid as Amount, description as Action, 1 as canUpload from PictureRequest where uid=$uid order by created desc";
$mylist=db::rows($sql);
$o=array();
foreach($mylist as $offer){
 $status="running";
 if($offer['status']<=0){
    $status='paused';
 }
 if($offer['Amount']==0) $offer['Amount']="done";
 if($offer['status']>0) {
   $offer['hint']="Pause";
   $offer['RedirectURL']="http://www.json999.com/pr/pause.php?id=".$offer['refId']."&uid=$uid&idfa=$idfa&h=".md5($uid.$idfa."dfsssf");
 }else{
    $offer['hint']="Run";
    $offer['RedirectURL']="http://www.json999.com/pr/pause.php?id=".$offer['refId']."&uid=$uid&cmd=Run&idfa=$idfa&h=".md5($uid.$idfa."dfsssf");
 }
 $offer['Action']=$status.": Click to manage";
 $offer['type']="CPA";
 $offer['Name']=$offer['Name']." ".$offer['uploadCount']." uploads";
 $o[]=$offer;
}
$ret=array(
"my"=>$o,
);

die(json_encode($ret));
