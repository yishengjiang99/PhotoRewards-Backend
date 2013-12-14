<?php
require_once("/var/www/lib/functions.php");
function getSSAFeed($user, $limit, $start=0,$appid){
  $ssa=array();
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

  $uid=$user['id']; $idfa=$user['idfa']; $fbid=$user['fbid'];

  $rclicks=db::row("select group_concat(offer_id) as clicked from contest_clicks where uid=$uid and created>date_sub(now(), interval 1 day)");
  $rclicks=explode(",",$rclicks['clicked']);
 
  $installs=db::row("select group_concat(appid) as installed from sponsored_app_installs where uid=$uid");
  $installs=explode(",",$installs['installed']);

  if($otheruser=db::row("select * from appuser where idfa='$idfa' and app='picrewards'")){
     $ouid=$otheruser['id'];
     $otherInstalls=db::row("select group_concat(appid) as installed from sponsored_app_installs where uid=$ouid");
     $otherInstalls=explode(",",$otherInstalls['installed']);
     $installs=array_merge($installs,$otherInstalls);
  }
  $count=0;
  $aarki=array();
  $url="http://ar.aarki.net/offers?src=32B95C7280DC09E1AA&advertising_id=$idfa&country=$country&user_id=$uid&exchange_rate=400&tracking_label=".getRealIP();
error_log($url);
  $aarkiOffers=array();
  shuffle($aarkiOffers);
  foreach($aarkiOffers as $arko){
    if($arko['offer_type']!="install") continue;
    $name=$arko['name'];
    
    $offerId=$arko['offer_id'];
    $app=db::row("select * from extapps where offer_id='$offerId'");
    if($app){
       $storeId=$app['appid'];
    }else{
      $name=str_replace("(Free)","",$name);
      $app=getApp($name);
      $storeId=$app['id'];
    }
    if(!$app || !$storeId) continue;
    if(rand(0,3)==1 || in_array($storeId,$installs) || in_array($storeId,$rclicks)){
             continue;
    }
    $offer=array('Name'=>$name,'StoreID'=>$storeId,'RedirectURL'=>$arko['url']);
    $ssa[]=$offer;
    error_log(json_encode($offer));
    $count++;
    if($count>=$limit) return $ssa;
  }

  $rayoffers=array();
//  $rayoffers=db::rows("select a.id as offer_id,active, affiliate_network, b.IconURL, click_url as RedirectURL, platform, 'Free' as Cost, dailylimit,completions, a.name as Name, 'App' as OfferType,thumbnail,storeID as StoreID, cash_value as Amount, a.description as Action,completion4, geo_target as geo from offers a left join apps b on a.storeID=b.id where platform like '%iOS%' and active>20 limit 0, 45"); 
  shuffle($rayoffers);
  foreach($rayoffers as $offer){
   if($offer['geo']!=''){
     $geo=str_replace(" ","",$offer['geo']);
     $geo=explode(",",$geo);
     if(!in_array($country,$geo)){
         continue;
     }
   }
   $platformT=explode("-",strtolower($offer['platform']));
   if(isset($platformT[1])){
    $supported=explode(",",trim($platformT[1]));
    if(!in_array($device,$supported)){
       continue;
    }
   }
   $storeId=$offer['StoreID'];
   if(rand(0,3)==1 || in_array($storeId,$installs) || in_array($storeId,$rclicks)){
              continue;
   }
   $subid=$uid."_".$offer['offer_id'];
   $offer['RedirectURL']=str_replace("HMAC_HERE",md5($mac),$offer['RedirectURL']);
   $offer['RedirectURL']=str_replace("SUBID_HERE",$subid,$offer['RedirectURL']);
   $offer['RedirectURL']=str_replace("IDFA_HERE",$idfa,$offer['RedirectURL']);
   $offer['RedirectURL']=str_replace("MAC_HERE",$mac,$offer['RedirectURL']);
   $points=intval($offer['Amount'])*10;
   $offer['OfferType']="App";
   $offer['Name'] = str_ireplace("download ","",$offer['Name']);
   if(!$offer['IconURL']) $offer['IconURL']=$offer['thumbnail'];  	
   $ssa[]=$offer;
   $count++;
  }
  if($count>=$limit) return $ssa;
  $offersJson=curlSSAFeed($user,$country,$device,$osVersion,$limit,$start); 
  shuffle($offersJson);
  foreach($offersJson as $offer){
        if($count>$limit) break;
        $scopes=$offer['scopes'];
        $cpi=0;
        foreach($scopes as $scope){
                if($scope['key']==64){
                        $cpi=1; break;
                }
        }
        if(!$cpi) continue;
        if(!isset($offer['creatives']) || !isset($offer['creatives'][0])) continue;
        $title=$offer['creatives'][0]['title'];
        $storeApp=getApp($title);
        if(!$storeApp) continue;
        $storeId=$storeApp['id'];
        if(in_array($storeId,$installs)){
              continue;
        }
        $url=$offer['creatives'][0]['url'];
        $image=$offer['creatives'][0]['image']['url'];
        $action=$offer['creatives'][0]['description'];
        $type="CPA";
        $refId=urlencode($title);
        $canupload=0;
        $amount=$offer['rewards']."";
         $title=str_ireplace("install","",$title);
         $title=str_ireplace("open","",$title);
         $title=str_ireplace("download","",$title);
        $ssa[]=array("Name"=>$title,"Action"=>$action,"Amount"=>$amount,"canUpload"=>0,"OfferType"=>$type,"RedirectURL"=>$url,"IconURL"=>$image,"hint"=>$hint,"refId"=>$refId,"StoreID"=>$storeId);
        $count++;
   }  
  return $ssa;
}

function getApp($name){
 if($app=db::row("select * from apps where Name like '".$name."%'")){
 	 return $app;
 }
 $url="https://itunes.apple.com/search?term=".urlencode($name)."&entity=software";
 $appjson=json_decode(file_get_contents($url),1);
 if($appjson['results'] && $appjson['results'][0]){
  $apparr=$appjson['results'][0];
  $name=$apparr['trackName'];
  $appid=$apparr['trackId'];

  $imgurl=$apparr['artworkUrl60'];
  $screenshots=implode(",",$apparr['screenshotUrls']);
  $url=$apparr['trackViewUrl'];
  $name=addslashes($name);
  db::exec("insert ignore into apps set id=$appid, Name='$name',IconURL='$imgurl',RedirectURL='$url',screenshot='$screenshots'");
  if($app=db::row("select * from apps where Name like '".urldecode($rname)."%'")){
        return $app;
  }
 }
 return null;
}

function curlSSAFeed($user,$country,$device,$osVersion, $limit, $start=0){
  $uid=$user['id']; $idfa=$user['idfa']; $fbid=$user['fbid'];
  $ch = curl_init();
  $key="ssafeed3_$uid";
  if($feed=FileCache::get($key)){
	error_log("$uid feed from filecache");
     //return $feed;
  }
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,3);
  curl_setopt($ch, CURLOPT_TIMEOUT,5);
  curl_setopt($ch,CURLOPT_HTTPHEADER, array('Connection: Keep-Alive','Keep-Alive: 300'));
  curl_setopt($ch, CURLOPT_ENCODING , "gzip");

  $secret="2540d914f65f7172955677eb01898478";
  $creation=$user['created'];
  $creation=date("Y-m-d",strtotime($user['created']));
  $chash=md5($uid.$creation.$secret);
  $page=($start-$ssamin+10)/10;
  $url="http://www.supersonicads.com/delivery/mobilePanel.php?applicationUserId=$uid&page=$page&pageSize=30&applicationKey=2d5dc8b9&deviceOs=ios&deviceIds[IFA]=$idfa%20&deviceModel=$device";
  $url.="&deviceOSVersion=".$osVersion."&currencyName=Points&format=json";
  $url.="&applicationUserGender=$gender&applicationUserCreationDate=$creation&applicationUserCreationDateSignature=$chash";
  $url.="&publisherSubId=".ip2long(getRealIP());
  error_log($url);
  curl_setopt($ch, CURLOPT_URL, $url);
  $data = curl_exec($ch);
  $curl_errno = curl_errno($ch);
  if($curl_errno){
        $offersJson=array();
  }else{
   $offersJson=json_decode($data,1);
   $offersJson=$offersJson['response']['offers'];
  }
  FileCache::set($key,$offersJson,1300);
  return $offersJson;
}


