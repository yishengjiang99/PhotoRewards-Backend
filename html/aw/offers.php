<?php
//die(file_get_contents("/var/www/html/aw/json.cache"));
require_once("/var/www/lib/functions.php");
$country="US";
$badge=array();

$uid=intval($_REQUEST['uid']);
if(true){
 $file="/var/www/html/pr/goodever_".$device.".json";
 $goodever=explode(",",file_get_contents($file)); 
 $data=json_decode(file_get_contents("/var/www/cache/badgecache$country"),1);
 if(!$data || $data['ttl']<time()){
        $everbadge="http://api.everbadge.com/offersapi/offers/json?api_key=9B8yxsmXx7xv7ujVFYJNf1373448697&os=ios&country_code=$country&t=".time();
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, $everbadge);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $badgeStr=curl_exec($ch);
        $everbadgeOffers = json_decode($badgeStr,1);
        curl_close($ch);
	error_log("calling $everbadge");
        $data=array("rows"=>$everbadgeOffers, "ttl"=>time()+60*10);
        file_put_contents("/var/www/cache/badgecache$country",json_encode($data));
 }
 $everbadgeOffers=$data['rows'];
 $et=$everbadgeOffers['data']['offers'];
 foreach($et as $row){
  $off['OfferType']="App";
  $off['Action']="Share a Screenshot of this app";
  $preview=explode("id",$row['preview_url']);
  if(!isset($preview[1]) || intval($preview[1])==0) continue;
  $off['StoreID']=$preview[1];
  $subid=$uid.",".$off['StoreID'];
  $off['RedirectURL']=$row['offer_url']."&device_id=$mac&aff_sub=$subid";
  $off['IconURL']=$row['thumbnail_url'];
  $off['hint']="Free App";
  $off['Name']=$row['public_name'];
  $namet=explode(" - ",$row['public_name']);
  $off['subtitle']=""; 
  if(isset($namet[1])){
   $off['subtitle']=$namet[1];
   $off['Name']=$namet[0];
  }
  $off['rating']=rand(0,4);
  $off['refId']=$preview[1];
  $pts=$row['payout']*300;
  $off['details']=rand(0,50)." reviews";
  $off['descriptions']="Great App!";
  $off['category']="Games";
  $off['Action']="Earn ".$pts." for each friend who download!";
  //$off['hint']="Tell Friends";
  $off['Amount']=$pts."";
  if($device=="ipod" && stripos($row['description'],"ipod")!==false) continue;
  $smap[$off['refId']]=1;
  $off['refId']=888;
  $badge[]=$off;
 }
}

$o=array();
$o=$badge;

$ret=array(
"offers"=>$o,
);
file_put_contents("/var/www/html/aw/json.cache",json_encode($ret));
die(json_encode($ret));
