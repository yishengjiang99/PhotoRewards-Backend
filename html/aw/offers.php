<?php
require_once("/var/www/lib/functions.php");
require_once("/var/www/lib/offers.php");
$uid=intval($_REQUEST['uid']);
$user=db::row("select * from appuser where id=$uid");
$idfa=$user['idfa'];
$mac=$user['mac'];
$h2=md5($uid.$idfa."ddfassffseesfg");
$country=$user['country'];
 $deviceInfo=$user['deviceInfo'];
 $device="iphone";
 if(stripos($deviceInfo,"ipod")!==false){
  $device='ipod';
 }
if(stripos($deviceInfo,"ipad")!==false){
 $device='ipad';
}
$osVersion="6.1";
if(stripos($deviceInfo,"7_")!==false || $mac=="ios7device"){
 $osVersion="7.0";
}
$badge=array();
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
  $off['Action']="Share what you know about this App";
  $preview=explode("id",$row['preview_url']);
  if(!isset($preview[1]) || intval($preview[1])==0) continue;
  $off['StoreID']=$preview[1];
  $subid=$uid.",".$off['StoreID'];
  $off['RedirectURL']=$row['offer_url']."&device_id=$mac&aff_sub=$subid";
  $off['IconURL']=$row['thumbnail_url'];
  $off['hint']="Free App";
  $off['Name']=$row['public_name'];
  $off['refId']=$preview[1];
  $pts=$row['payout']*100;
  if(isset($smap[$off['refId']])){
     continue;
  }
  if(!in_array($off['StoreID'],$goodever)){
      if($vcount<10 || rand(0,2)!=1) {
//         continue;
        }
  }
 
  $off['Amount']="Free";
  if($device=="ipod" && stripos($row['description'],"ipod")!==false) continue;
  $smap[$off['refId']]=1;
  $badge[]=$off;
 }
}
$ret=array("offers"=>$badge);
die(json_encode($ret));
