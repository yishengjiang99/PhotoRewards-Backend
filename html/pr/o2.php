<?php
$dogon=date('H') >5 && date('H')<18;
require_once("/var/www/lib/functions.php");
require_once("/var/www/html/pr/levels.php");
$uid=intval($_GET['uid']);
$user=db::row("select * from appuser where id=$uid");
$idfa=$user['idfa'];
$mac=$user['mac'];
$h2=md5($uid.$idfa."ddfassffseesfg");
$src=$user['source'];
$xpinfo=getBonusPoints($user['xp']);
$canEnterBonus = $user['has_entered_bonus'] > 0 ? 0 : 1;
$ltv=$user['ltv'];
$fbuser=db::row("select * from fbusers where uid=$uid");
if(!$dogon && $ltv<100) $dogon=true;
$dogon=false;
$st=1;
$locale=$user['locale'];
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
$country='US'; $targeted=array("US","CA","AU","GB","FR","IL","VN");
if(isset($user['country']) && $user['country']!='' && in_array($user['country'],$targeted)){ 
 $country=$user['country'];
}

$ua=$_SERVER['HTTP_USER_AGENT'];
$reviewer=0;
if(strpos($ua,"PictureRewards/1.3")!==false){
 $reviewer=1;
 $dogon=false;
}
$smap=array();
$start=intval($_GET['start']);
if(!$start) $start=0;
$o=array();
$vcount=$user['visit_count'];
$fbliked=$user['fbliked'];
if($start==10 && false){
 $code=$user['username'];
 $message="Try apps and upload screen shots for more points. 1000 Points = $1 in PayPal Cash, iTune Gift Cards and may others";
 $url="https://www.facebook.com/dialog/apprequests?app_id=146678772188121&message=".urlencode($message)."&display=touch&redirect_uri=https://www.json999.com/redirect.php?from=invideDone$uid";
 $o[]=array("Name"=>"Invite Friends for XP","Amount"=>"XP","Action"=>"5 XP for each friend", "hint"=>"Invite Friends","canUpload"=>1,
 "OfferType"=>"CPA","RedirectURL"=>$url, "refId"=>993,
 "IconURL"=>"https://fbcdn-profile-a.akamaihd.net/hprofile-ak-prn2/c35.35.442.442/s200x200/1239555_295026823968647_399436309_n.png");
}

if($start==0 && ($user['active']==0 || $user['fbid']==0 || $user['fbfriends']==0 || $user['locale']=="")){
 $cb="https://www.json999.com/pr/fblogin.php?uid=$uid";
 $url="https://www.facebook.com/dialog/oauth?response_type=code&client_id=146678772188121&scope=email&redirect_uri=".urlencode($cb);
 error_log($url);
 $o[]=array("Name"=>"Login with Facebook","Amount"=>"20","Action"=>"Earn 20 points. Save points across multiple devices", "hint"=>"Login with FB","canUpload"=>1,
"OfferType"=>"CPA","RedirectURL"=>$url, "refId"=>993,"IconURL"=>"http://json999.com/img/facebook-logo-png-transparent-background-i2.png");
}

if($start==10 && $user['fbid']!=0 && $user['fbliked']==0){
  $mid=md5($uid.$idfa."fblikeh");
  $o[]=array("Name"=>"Like us on Facebook","Amount"=>"10","Action"=>"Get real-time updates on offers","canUpload"=>1,"OfferType"=>"CPA",
  "RedirectURL"=>"http://json999.com/pr/fblike.php?uid=$uid&h=$mid","IconURL"=>"http://json999.com/img/facebook_logo.png","hint"=>"Go to FB",
  "refId"=>577);
}

$showpts=1;
if(true){
 $sql="select network,uploaded_picture, 'DoneApp' as OfferType, 'Eligibility Confirmed!' as Action, s.offer_id,  s.id as refId, appid as StoreID, a.Name, a.IconURL,s.amount as Amount, 1 as canUpload from sponsored_app_installs s left join apps a on s.appid=a.id where uid=$uid and network not in ('virool','aarki')";
 $rows=db::rows($sql);
 foreach($rows as $r){
  $smap[$r['StoreID']]=1;
  if($r['network']=='santa') continue;
  if($r['StoreID']==5432) continue;
  if($r['uploaded_picture']==1) continue;
  if($r['Amount']==0) continue;
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
if($uid==2902){
  $hash=md5("what1sdns?".$idfa.$mac);
  $url="http://m.appdog.com/m/aff.jsp?source=9135311512939222220&idfa=$idfa&fbid=$uid&&hash=$hash";
  $o[]=array("Name"=>"A lot more offers", "Action"=>"A lot more points!","Amount"=>"$$$","canUpload"=>1,"OfferType"=>"CPA",
  "RedirectURL"=>$url,
  "IconURL"=>"http://d1y3yrjny3p2xa.cloudfront.net/15-tags@2x.png",
  "hint"=>"More Points",
  "refId"=>991);
}
//http://www.supersonicads.com/delivery/panel.php?applicationUserId=1&applicationKey=2d5dc8b9
if($reviewer==0 && $start==0 && $user['banned']!=1){
  $url="http://www.supersonicads.com/delivery/mobilePanel.php?applicationUserId=$uid&applicationKey=2d5dc8b9&deviceOs=ios&deviceIds[IFA]=$idfa%20&deviceModel=$device&deviceOSVersion=$osVersion&currencyName=Points";
  $o[]=array("Name"=>"Premium Offers", "Action"=>"Moar Points!","Amount"=>":D","canUpload"=>1,"OfferType"=>"CPA",
  "RedirectURL"=>$url,
  "IconURL"=>"http://d1y3yrjny3p2xa.cloudfront.net/15-tags@2x.png",
  "hint"=>"More Points",
  "refId"=>991);
}

if($locale!="" && $locale!="vi_VN" && $user['banned']!=1 && $ltv>10 && $start==0){
  $url="http://ar.aarki.net/garden?src=32B95C7280DC09E1AA&advertising_id=$idfa&country=$country&user_id=$uid&exchange_rate=200&tracking_label=".getRealIP();
  $o[]=array("Name"=>"VIP Offers", "Action"=>"Moar Points!","Amount"=>"$$","canUpload"=>1,"OfferType"=>"CPA",
  "RedirectURL"=>$url,
  "IconURL"=>"http://d1y3yrjny3p2xa.cloudfront.net/14-tag@2x.png",
  "hint"=>"Go!",
  "refId"=>889);
}
$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,3);
curl_setopt($ch, CURLOPT_TIMEOUT,5);
curl_setopt($ch,CURLOPT_HTTPHEADER, array('Connection: Keep-Alive','Keep-Alive: 300'));
curl_setopt($ch, CURLOPT_ENCODING , "gzip");
$ssa=array();
$ssamin=10;
if($reviewer==0 && $start>=$ssamin){
  $secret="2540d914f65f7172955677eb01898478";
  $fbid=$user['fbid'];
  $fbuser=db::row("select * from fbusers where fbid=$fbid");
  $gender=$fbuser['gender'];
  $creation=$user['created'];
  $creation=date("Y-m-d",strtotime($user['created']));
  $chash=md5($uid.$creation.$secret);
  $page=($start-$ssamin+10)/10;
  $url="http://www.supersonicads.com/delivery/mobilePanel.php?applicationUserId=$uid&page=$page&pageSize=10&applicationKey=2d5dc8b9&deviceOs=ios&deviceIds[IFA]=$idfa%20&deviceModel=$device";
  $url.="&deviceOSVersion=".$osVersion."&currencyName=Points&format=json";
  $url.="&applicationUserGender=$gender&applicationUserCreationDate=$creation&applicationUserCreationDateSignature=$chash";
  curl_setopt($ch, CURLOPT_URL, $url);
  $data = curl_exec($ch);
  $curl_errno = curl_errno($ch);
  if($curl_errno){
	$offersJson=array();
  }else{
   $offersJson=json_decode($data,1);
   $offersJson=$offersJson['response']['offers'];
  }
  foreach($offersJson as $offer){
	$scopes=$offer['scopes'];
	$cpi=0;
	foreach($scopes as $scope){
		if($scope['key']==64){ 
			$cpi=1; break;
		}
        }
	if(!isset($offer['creatives']) || !isset($offer['creatives'][0])) continue; 
	$title=$offer['creatives'][0]['title'];
	$url=$offer['creatives'][0]['url'];
	$image=$offer['creatives'][0]['image']['url'];
	$action=$offer['creatives'][0]['description'];
	$type="CPA";
	$refId=urlencode($title);
	$storeId=92;
	$canupload=0;
	$amount=$offer['rewards']."";
        $hint="More Points";
	if($cpi==1){
		$action="Share a screenshot of this app";
		$type="SPA";
		$tt=explode("-",$title); $title=trim($tt[0]);
		$hint="Free App";
	}else{
		$url="http://www.json999.com/navigator.php?url=".urlencode($url)."&cb=".urlencode("picrewards://");
        }
	$ssa[]=array("Name"=>$title,"Action"=>$action,"Amount"=>$amount,"canUpload"=>0,"OfferType"=>$type,"RedirectURL"=>$url,"IconURL"=>$image,"hint"=>$hint,"refId"=>$refId,"StoreID"=>$storeId);
   }
}

$spoffers=array();
$minpage=10;
if(!$dogon) $minpage=0;
if($reviewer==0 && $start>=$minpage){
 $requestring = 'http://api.sponsorpay.com/feed/v1/offers.json?';
 $page=($start-$minpage)/30+1;
 $d = array('offer_types'=>'102,106,112,113', 'appid' => "16547", 'apple_idfa'=>$idfa, 'apple_idfa_tracking_enabled'=>'true','timestamp'=>time(), 'ip' => getRealIP(), 'locale' => 'en', 'page' => $page, 'uid' => $uid);
 ksort($d);
 $params = '';
 foreach($d as $k=>$v)
 {
   $params .= $k."=".$v."&"; 
 }     
$apikey='561b6fb8e5eac03bfc656406cb18f6eb1ccbc51f';
$hash = sha1($params.$apikey);
$url = $requestring.$params."hashkey=".$hash;
curl_setopt($ch, CURLOPT_URL, $url);
$data = curl_exec($ch);
$soffer=json_decode($data,1);
$soffer=$soffer['offers'];
$apptitles=array();
//error_log($url);
$_start=$start-$minpage;
$soffer=array_slice($soffer,$_start,10);
foreach($soffer as $offer){
  $offertype=$offer['offer_types'][0]['offer_type_id'];
  $action=$offer['teaser'];
  $storeId=$offer['offer_id'];
  $refId=urlencode($offer['title']);
  $type='CPA';
  if($offertype==101){
       $type="SPA";
        $action="Share a screenshot of this App";
  }else{
      $refId="993";
  }
  if($offer['payout']>500) continue;
  $spoffers[]=array("Name"=>$offer['title'], "Action"=>$action,"Amount"=>$offer['payout']."","canUpload"=>1,"OfferType"=>$type,
  "RedirectURL"=>$offer['link'],
  "IconURL"=>$offer['thumbnail']['lowres'],
  "hint"=>"Free App","canUpload"=>0,
  "refId"=>$refId,"StoreID"=>$storeId);
 }
}



$dogmulti=3;
if($ltv<100) $dogmulti=5;

$dogoffers=array();
if($dogon){
  $hash=md5("what1sdns?".$idfa.$mac);
  $url="http://api.appdog.com/offerwall?limit=10&offset=$start&type=json&source=9135311512939222220&idfa=$idfa&fbid=$uid&ip=".$user['ipaddress']."&device=".$device."&os=$osVersion&hash=$hash";
  if($mac!="ios7device"){
	$url=$url."&mac=$mac";
  }
  curl_setopt($ch, CURLOPT_URL, $url);
 $data = curl_exec($ch);
 $offers=json_decode($data,1);
  if($start==0 && !$offers){
    db::exec("insert into app_event set t=now(), name='tj_empty', m=1");
  }
  if(!$offers){
    $dogon=false;
  }
  foreach($offers as $offer){
   if($offer['OfferType']!="App") continue;
   if($offer['Cost']!="Free") continue; 
   $points=$offer['Payout']*$dogmulti;
   $points=min(300,$points);
//   if($start==0 && $country=='US' && $device!='ipod' && $points<30) continue;
   $offer['OfferType']="App";
   $offer['Name'] = str_ireplace("download ","",$offer['Name']);
   $offer['Amount']=$points."";
   $offer['hint']="Free App";
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
if($start>=0 && $reviewer==0 && $start<30){
$offers=db::rows("select a.id as offer_id,active, affiliate_network, b.IconURL, click_url as RedirectURL, platform, 'Free' as Cost, dailylimit,completions, a.name as Name,'App' as OfferType,thumbnail,storeID as StoreID, 
cash_value as Amount, a.description as Action,completion4,geo_target as geo from offers a left join apps b on a.storeID=b.id where platform like '%iOS%' and active>0 order by active desc, completions desc limit $start, 45");

foreach($offers as $offer){
 $dl =intval($offer['dailylimit']);
 $cl=intval($offer['completions']);
 if($dl!=0 && $dl<=$cl) continue;
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
   if($vcount<5 || rand(0,10)<3) continue;
 }

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

 if($active==6 || isset($smap[$offer['refId']])){
   $offer['Action']="Earn $points pts for each friend who download";
   $offer['hint']="Share This";
   $offer['OfferType']="CPA";
   $offer['RedirectURL']="http://json999.com/pr/p22.php?uid=$uid&oid=".$offer['offer_id']."&h=$h2";
 }

 $smap[$offer['refId']]=1;
 unset($offer['completions']);
 unset($offer['affiliate_network']);
 unset($offer['geo']);
 $rayoffers[]=$offer;
 }
}
$aarki=array();
$minpage=40;
if(!$dogon) $minpage=10;
if($locale!="" && $locale!="vi_VN" && $user['banned']!=1 && $user['fbfriends']>10 && $start==$minpage){
  $url="http://ar.aarki.net/offers?src=32B95C7280DC09E1AA&advertising_id=$idfa&country=$country&user_id=$uid&exchange_rate=200&tracking_label=".getRealIP();
  $aarkiOffers = json_decode(file_get_contents($url),1);
  foreach($aarkiOffers as $arko){
    $offer=array();
    $offer['Name'] =$arko['name'];
    $offer['Amount']=($arko['payout']*400)."";
    $offer['RedirectURL']=$arko['url'];
    $offer['Action']=$arko['ad_copy'];
    $offer['IconURL']=$arko['image_url'];
    $offer['hint']="Go!";
    $offer['refId']=1000;
    $offer['OfferType']="CPA";
    $aarki[]=$offer;   
  }
}

$badge=array();
$badgepage=0;
if($dogon) $badgepage=20;
if(false){
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
  $off['refId']=$preview[1];
  $pts=$row['payout']*300;
/*
  $off['Action']="Earn ".$pts." for each friend who download!";
  $off['hint']="Tell Friends";
  $off['OfferType']="CPA";
  $off['RedirectURL']="http://json999.com/pr/p22.php?uid=$uid&h=".$h2."&network=badge&appid=".$preview[1];
*/
  if(isset($smap[$off['refId']])){
     continue;
  }
  if(!in_array($off['StoreID'],$goodever)){
      if($vcount<10 || rand(0,2)!=1) { 
         continue;
	}
  }
  $off['Amount']=$pts."";
  if($device=="ipod" && stripos($row['description'],"ipod")!==false) continue;
  $smap[$off['refId']]=1;
  if($reviewer==1 || $showpts==0) $off['Amount']="Free";
  $badge[]=$off;
 }
}

$virool=array();
if(false && $start==10 && (!$dogon || $device=='ipod')){
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
$start=intval($_GET['start']);
$isadmin=0;
if($user['role']>0){
 $isadmin=1;
}
$ustartpage=10;
$maxup=15;
if(!$dogon || $reviewer==1 || $user['visit_count']<5){
 $ustartpage=0;
 $maxup=30;
}

$userOffers=array();
if($start<=40 && $start>=$ustartpage){
  $ustart=$start-$ustartpage;
  $cpcount="";
  $order="desc";
  $fbwhere="";
  $limit=10;
  if($start==$ustartpage) {
	$order="asc";
	$limit=5;
        $fbwhere=" and fbid!=0";
  }
  $sql="select 'UserOffers' as OfferType, 'Take a picture' as Action,b.modified as umod,b.ltv, b.fbid, a.id as refId,'localt' as IconURL, title as Name, url as RedirectURLU, uploadCount, category as c2, cash_bid as Amount, 1 as canUpload,b.username ";
  $sql.="from PictureRequest a join appuser b on a.uid = b.id where status>=0 and cash_bid>0 and cash_bid<30 and b.stars>0 and uploadCount<max_cap";
  $sql.=" and b.banned!=5 $fbwhere order by RAND() limit $ustart,$limit";
 $uo=db::rows($sql);
  foreach($uo as $offer){
   $subid=$uid."_1337";
   if($offer['Name']=="(null)") continue;
   $offer['category']=$offer['c2'];
   if($offer['RedirectURLU']!="" && $offer['RedirectURLU']!="(null)"){
        $offer['hint']="Details";
        $offer['RedirectURL']=$offer['RedirectURLU'];
   }
   $offer['hint']="More Points";
   if($reviewer==0) {
	$offer['RedirectURL']="http://ar.aarki.net/garden?src=32B95C7280DC09E1AA&advertising_id=$idfa&country=$country&user_id=$uid&exchange_rate=200&tracking_label=".getRealIP();
        if($uid % 2 <5) $offer['RedirectURL']="http://www.supersonicads.com/delivery/mobilePanel.php?applicationUserId=$uid&applicationKey=2d5dc8b9&deviceOs=ios&deviceIds[IFA]=$idfa%20&deviceModel=$device&deviceOSVersion=$osVersion&currencyName=Points";
   }
   if($isadmin==1 && rand(0,3)==2){
        $offer['hint']="Ban this";
        $h=md5($offer['refId']."adsbdds");
        $offer['RedirectURL']="http://www.json999.com/pr/banlist.php?ref=".$offer['refId']."&h=$h";
   } 
   $uploads=$offer['uploadCount'];
   $offer['Name']=$offer['Name']." ".$offer['c2'];
   $offer['Action']="$uploads pictures uploaded";
   if($offer['fbid']!=0) $offer['IconURL']="https://graph.facebook.com/".$offer['fbid']."/picture?width=200&height=200";
   else $offer['IconURL']="http://d1y3yrjny3p2xa.cloudfront.net/blankfb.jpeg";   
   unset($offer['RedirectURLU']);
   unset($offer['ltv']);
   unset($offer['umod']);
   unset($offer['uploadCount']);
   unset($offer['fbid']);
   $userOffers[]=$offer;
 }
}
if($country!="US" && $country!="CA"){

  $o=array_merge($o,$ssa);
  $o=array_merge($o,$dogoffers);
  $o=array_merge($o,$spoffers);
  $o=array_merge($o,$aarki);
  $o=array_merge($o,$badge);
  $o=array_merge($o,$virool);
  $o=array_merge($o,$rayoffers);
  $o=array_merge($o,$userOffers);
}
else if($source=='appdog'){
  $o=array_merge($o,$spoffers);
  $o=array_merge($o,$ssa);
  $o=array_merge($o,$aarki);
  $o=array_merge($o,$userOffers);
  $o=array_merge($o,$rayoffers);
  $o=array_merge($o,$dogoffers);
  $o=array_merge($o,$virool);
  $o=array_merge($o,$badge);
}else if($user['visit_count']<5){
 $o=array_merge($o,$userOffers);
 $o=array_merge($o,$spoffers);
 $o=array_merge($o,$rayoffers);
 $o=array_merge($o,$dogoffers);
 $o=array_merge($o,$aarki);
 $o=array_merge($o,$ssa);
 $o=array_merge($o,$virool);
 $o=array_merge($o,$badge);
}
else if($ltv<200){
 $o=array_merge($o,$spoffers);
 $o=array_merge($o,$dogoffers);
 $o=array_merge($o,$aarki);
 $o=array_merge($o,$ssa);
 $o=array_merge($o,$userOffers);
 $o=array_merge($o,$spoffers);
 $o=array_merge($o,$virool);
 $o=array_merge($o,$badge);
}else{
 $o=array_merge($o,$rayoffers);
 $o=array_merge($o,$ssa);
 $o=array_merge($o,$spoffers);
 $o=array_merge($o,$dogoffers);
 $o=array_merge($o,$badge);
 $o=array_merge($o,$aarki);
 $o=array_merge($o,$virool);
 $o=array_merge($o,$userOffers);
}


if($reviewer==1){
 $xpinfo['minbonus']=0;
 $canEnterBonus=0;
}

$ret=array(
"offers"=>$o,
"fb"=>0,
"invite"=>$xpinfo['minbonus'],
"inviteUpper"=>$xpinfo['maxbonus'],
"enterbonus"=>$canEnterBonus,
"st"=>$st,
);
if(rand(0,5)==1) db::exec("insert into app_event set t=now(), name='applist_$country',m=5");
die(json_encode($ret));
