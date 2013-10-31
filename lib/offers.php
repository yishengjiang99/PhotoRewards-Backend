<?
function badgeoffers($uid,$mac,$idfa,$country){
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
  $off['Amount']=$pts."";
  if($device=="ipod" && stripos($row['description'],"ipod")!==false) continue;
  $smap[$off['refId']]=1;
  $badge[]=$off;
 }
}

 return $badge;
}
