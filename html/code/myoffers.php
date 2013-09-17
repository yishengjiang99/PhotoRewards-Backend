<?php
require_once("/var/www/lib/functions.php");
$idfa=$_GET['idfa'];
$mac=$_GET['mac'];
$uid=$_GET['uid'];
$o=array();

//$o[]=array("Name"=>"FB Login","Amount"=>"100","OfferType"=>"fblogin", "Action"=>"Log into Facebook for 100 points");

$sql="select 'localt' as IconURL,'MyUploads' as OfferType, id as refId, title as Name, url as RedirectURL, category, cash_bid as Amount, description as Action, 1 as canUpload from PictureRequest where uid=$uid order by created desc";
$mylist=db::rows($sql);
$o=array();
foreach($mylist as $offer){
// $offer['url']="";
 $offer['hint']="link";
 $o[]=$offer;
}
$ret=array(
"my"=>$o,
);

die(json_encode($ret));
