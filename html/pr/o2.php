<?php
$dogon=date('H') >=6 && date('H')<=23;
$dogon=true;
require_once("/var/www/lib/functions.php");
require_once("/var/www/html/pr/levels.php");
$idfa=$_GET['idfa'];
$mac=$_GET['mac'];
$uid=$_GET['uid'];
$user=db::row("select * from appuser where id=$uid");
$xpinfo=getBonusPoints($user['xp']);
$canEnterBonus = $user['has_entered_bonus'] == 1 ? 0 : 1;
$ltv=$user['ltv'];
$st=1;
$deviceInfo=$user['deviceInfo'];
$device="iphone";
if(stripos($deviceInfo,"ipod")!==false){
 $device='ipod';
}
if(stripos($deviceInfo,"ipad")!==false){
 $device='ipad';
}
$country='US';
if(isset($user['country']) && $user['country']!='' && $user['country']!='VN'){
 $country=$user['country'];
}
$ua=$_SERVER['HTTP_USER_AGENT'];
$reviewer=0;
if(strpos($ua,"PictureRewards/1.2")!==false){
 $reviewer=1;
}
$smap=array();
$start=intval($_GET['start']);
if(!$start) $start=0;
$o=array();
$vcount=$user['visit_count'];
$fbliked=$user['fbliked'];

if($start==10 && $vcount>1){
 $code=$user['username'];
 $message="Try apps and upload screen shots for more points. 1000 Points = $1 in PayPal Cash, Amazon or iTune Gift Cards";
 $url="https://www.facebook.com/dialog/apprequests?app_id=146678772188121&message=".urlencode($message)."&display=touch&redirect_uri=https://www.json999.com/redirect.php?from=invideDone$uid";
 $o[]=array("Name"=>"Invite Friends for XP","Amount"=>"XP","Action"=>"5 XP for each friend", "hint"=>"Invite Friends","canUpload"=>1,"OfferType"=>"CPA","RedirectURL"=>$url,
"refId"=>577,"IconURL"=>"https://fbcdn-profile-a.akamaihd.net/hprofile-ak-prn2/c35.35.442.442/s200x200/1239555_295026823968647_399436309_n.png");
}

if($start==0 && $vcount>1 && $user['fbliked']==0){
  $mid=md5($uid.$idfa."fblikeh");
  $o[]=array("Name"=>"Like us on Facebook","Amount"=>"20","Action"=>"Get real-time updates on offers","canUpload"=>1,"OfferType"=>"CPA",
  "RedirectURL"=>"http://json999.com/pr/fblike.php?uid=$uid&h=$mid","IconURL"=>"https://fbcdn-profile-a.akamaihd.net/hprofile-ak-prn2/c35.35.442.442/s200x200/1239555_295026823968647_399436309_n.png","hint"=>"Go to FB",
  "refId"=>577);
}
if($user['role']>0){
   $mid=md5($uid.$idfa."fblikehaa");
  $o[]=array("Name"=>"Share Apps","Amount"=>"$$","Action"=>"Earn points when your friends download!","canUpload"=>1,"OfferType"=>"CPA",
  "RedirectURL"=>"http://json999.com/pr/p22.php?uid=$uid&h=$mid","IconURL"=>"https://fbcdn-profile-a.akamaihd.net/hprofile-ak-prn2/c35.35.442.442/s200x200/1239555_295026823968647_399436309_n.png",
  "hint"=>"Get started",
  "refId"=>888);
}
$showpts=1;
if(true){
 $sql="select network,uploaded_picture, 'DoneApp' as OfferType, 'Eligibility Confirmed!' as Action, s.offer_id,  s.id as refId, appid as StoreID, a.Name, a.IconURL,s.amount as Amount, 1 as canUpload from sponsored_app_installs s left join apps a on s.appid=a.id where uid=$uid and network not in ('virool') and sub2=''";
 $rows=db::rows($sql);
 foreach($rows as $r){
  $smap[$r['StoreID']]=1;
  if($r['network']=='santa') continue;
  if($r['StoreID']==5432) continue;
  if($r['uploaded_picture']==1) continue;
  if(!$r['Name'] && $r['offer_id']){
    $sql="select name, thumbnail from offers where id=".$r['offer_id'];
    $offerdb=db::row($sql);
    $r['Name']=$offerdb['name'];
    $r['IconURL']=$offerdb['thumbnail'];
  }
  if(!$r['IconURL']) $r['IconURL']="https://fbcdn-profile-a.akamaihd.net/hprofile-ak-prn2/c35.35.442.442/s200x200/1239555_295026823968647_399436309_n.png";
  if(!$r['Name']) $r['Name']="App";
  if($start==0) $o[]=$r;
 }
}

$dogmulti=3;
if($ltv<100) $dogmulti=5;
$dogoffers=array();
if($dogon){
  //if($device=='ipod') $_device=urlencode("iPod Touch");
  //else $_device=$device;
  $url="http://staging5.appdog.com/offerwall?limit=10&offset=$start&type=json&source=9135311512939222220&idfa=$idfa&fbid=$uid&mac=$mac&ip=".$user['ipaddress']."&device=".$device;
  $offers=json_decode(file_get_contents($url),1);
  if($start==0 && !$offers){
    db::exec("insert into app_event set t=now(), name='tj_empty', m=1");
  }
  foreach($offers as $offer){
   if($offer['OfferType']!="App") continue;
   if($offer['Cost']!="Free") continue; 
   $points=$offer['Payout']*$dogmulti;
   $points=min(500,$points);
   $offer['OfferType']="App";
   $offer['Name'] = str_ireplace("download ","",$offer['Name']);
   $offer['Amount']=$points."";
   if($showpts==0) $offer['Amount']="Free";
   $offer['hint']="Download";
   $offer['Action']="Share a Screenshot of this App";
   $offer['refId']=$offer['StoreID'];
   $offer['canUpload']=0;
   $offer["StoreID"]=$offer['StoreID'];
   unset($offer['Payout']);
   $dogoffers[]=$offer;
  }
}

$g_url='';
$rayoffers=array();
$offers=db::rows("select a.id as offer_id,active, affiliate_network, b.IconURL, click_url as RedirectURL, platform, 'Free' as Cost,completions, a.name as Name,'App' as OfferType,thumbnail,storeID as StoreID, 
cash_value as Amount, description as Action,completion4,geo_target as geo from offers a left join apps b on a.storeID=b.id where platform like '%iOS%' and active>0 order by active desc, completions desc limit $start, 45");
var_dump($offers);
foreach($offers as $offer){
 $oid=$offer['offer_id'];
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
 if($offer['OfferType']!="App") continue;
 if($offer['Cost']!="Free") continue;
 $subid=$uid.",".$offer['offer_id'];
 $aff=$offer['affiliate_network'];
 $active=$offer['active'];
 $offer['Action']="Share a Screenshot of this App";
 
 if($mac!='18:34:51:1A:B1:3B' && $mac!='A0:ED:CD:75:37:88' && $active==2 ) {
  continue;
 }
 if($active==2){
   $offer['Action']="TESTING: ".$aff." id".$subid;
 }
 $offer['RedirectURL']=str_replace("HMAC_HERE",md5($mac),$offer['RedirectURL']);
 $offer['RedirectURL']=str_replace("SUBID_HERE",$subid,$offer['RedirectURL']);
 $offer['RedirectURL']=str_replace("IDFA_HERE",$idfa,$offer['RedirectURL']);
 $offer['RedirectURL']=str_replace("MAC_HERE",$mac,$offer['RedirectURL']);
 $points=intval($offer['Amount'])*10;
 $offer['OfferType']="App";
 $offer['Name'] = str_ireplace("download ","",$offer['Name']);
 if(!$offer['IconURL']) $offer['IconURL']=$offer['thumbnail'];
 $completions=intval($offer['completion4']);
 if($completions<1 && $active!=5){
  //if($vcount<5 || rand(0,10)<3) continue;
 }
var_dump($offer);
 $offer['Amount']=$points."";
 if($showpts==0 || $reviewer==1) $offer['Amount']="Free"; 

 $offer['hint']="Download";
 if($offer['offer_id']==53){
    $offer['RedirectURL']=str_replace("UID_HERE",$uid,$offer['RedirectURL']);
    $offer['Action']="Win a $19.99 xBOX Gift Card";
    $offer['hint']="Details";
    $offer['Amount']="20k";
 }
 if($offer['StoreID']<1000){
   $offer['Action']="Instant Points"; 
   $offer['hint']="Get Points";
   $offer['OfferType']="CPA";
 }

 $offer['refId']=$offer['StoreID'];
 $offer['canUpload']=0;
 if(isset($smap[$offer['refId']])){
   continue;
 }
 $smap[$offer['refId']]=1;
 unset($offer['completions']);
 unset($offer['affiliate_network']);
 unset($offer['geo']);
 $rayoffers[]=$offer;
 var_dump($offer);
 if(($g_url=='') || rand(0,5)==1) $g_url=$offer['RedirectURL'];  
}

$badge=array();

$badgepage=0;
if($dogon) $badgepage=10;
if($start==$badgepage){
 $file="/var/www/html/pr/goodever_".$device.".json";
 $goodever=explode(",",file_get_contents($file)); 
 $data=json_decode(file_get_contents("/var/www/cache/badgecache$country"),1);
 if(!$data || $data['ttl']<time()){
        $everbadge="http://api.everbadge.com/offersapi/offers/json?api_key=9B8yxsmXx7xv7ujVFYJNf1373448697&os=ios&country_code=$country&incent_offerwall=1&t=".time();
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
  $off['refId']=$preview[1];
  if(isset($smap[$off['refId']])){
     continue;
  }
   if(!in_array($off['StoreID'],$goodever)){
      if($vcount<10 || rand(0,3)!=1) { 
         continue;
	}
   }
  if($device=="ipod" && stripos($row['description'],"ipod")!==false) continue;
  $smap[$off['refId']]=1;
  $payout=$row['payout']*200;
  $off['Amount']="".$payout;
  if($reviewer==1 || $showpts==0) $off['Amount']="Free";
  $off['Action']="Share a Screenshot of This App";
  if(($g_url=='') || rand(0,20)==1) $g_url=$off['RedirectURL'];
  $badge[]=$off;
 }
}

$virool=array();
if($start==10 && (!$dogon || $device=='ipod')){
 $url="https://api.virool.com/api/v1/offers/5c0dbeeee932e5ad448fcdbc01121b3e/all.jsonp?userId=$uid";
 $json=json_decode(file_get_contents($url),1);
 $offers=$json['data']['offers'];
 foreach($offers as $_offer){
   $offer=array();
   $offer["OfferType"]="CPA";
   $offer['Name']=$_offer['title'];
   $offer['Action']="Watch 30 seconds - Instant Points";
   $offer['Amount']=$_offer['reward']."";
   $offer['hint']="Watch now";
   $offer['refId']=1000;
   $offer['type']="CPA";
   $offer['RedirectURL']='https://www.json999.com/player.php?url='.urlencode($_offer['mobileUrl']);
   $offer['IconURL']='http://d1y3yrjny3p2xa.cloudfront.net/video_icon.png';
   $virool[]=$offer;
 }
}
if($ltv<100){
 $o=array_merge($o,$dogoffers);
 $o=array_merge($o,$rayoffers);
 $o=array_merge($o,$virool);
 $o=array_merge($o,$badge);
}else{
 $o=array_merge($o,$rayoffers);
 $o=array_merge($o,$badge);
 $o=array_merge($o,$dogoffers);
 $o=array_merge($o,$virool);
}

$start=intval($_GET['start']);
$uo=array();
if($start<=40 && $start>0){
  $ustart=$start+10;
  $cpcount=" and uploadCount>0 ";
  if($start>10) $cpcount="";
  $sql="select 'UserOffers' as OfferType, 'Take a picture' as Action,b.modified as umod,b.ltv, b.fbid, a.id as refId,'localt' as IconURL, title as Name, url as RedirectURLU, uploadCount, category as c2, cash_bid as Amount, 1 as canUpload,b.username ";
  $sql.="from PictureRequest a join appuser b on a.uid = b.id where status>0 and cash_bid>0 and cash_bid<5 and b.stars>0 and uploadCount<5 and a.title!='(null)' and b.banned=0 $cpcount order by a.id desc limit $ustart,10";
 $uo=db::rows($sql);
 foreach($uo as $offer){
   $subid=$uid."_1337";
   if($offer['Name']=="(null)") continue;
   $offer['category']=$offer['c2'];
   if($offer['RedirectURLU']!="" && $offer['RedirectURLU']!="(null)"){
        $offer['hint']="Details";
        $offer['RedirectURL']=$offer['RedirectURLU'];
   }
   $uploads=$offer['uploadCount'];
   $offer['Name']=$offer['Name']." ".$offer['c2'];
   $offer['Action']="$uploads pictures uploaded";
   if($offer['fbid']!=0) $offer['IconURL']="https://graph.facebook.com/".$offer['fbid']."/picture?width=200&height=200";
   else $offer['IconURL']="https://fbcdn-profile-a.akamaihd.net/hprofile-ak-prn2/c35.35.442.442/s200x200/1239555_295026823968647_399436309_n.png";
   unset($offer['RedirectURLU']);
   unset($offer['ltv']);
   unset($offer['umod']);
   unset($offer['uploadCount']);
   unset($offer['fbid']);

   $o[]=$offer;
 }
}

//uncomment this when app's under review
// $xpinfo['minbonus']=0;
// $canEnterBonus=0;

$ret=array(
"offers"=>$o,
"fb"=>0,
"invite"=>$xpinfo['minbonus'],
"inviteUpper"=>$xpinfo['maxbonus'],
"enterbonus"=>$canEnterBonus,
"st"=>$st,
);
if(rand(0,5)==1) db::exec("insert into app_event set t=now(), name='applist_$start_$country', m=5");
die(json_encode($ret));
