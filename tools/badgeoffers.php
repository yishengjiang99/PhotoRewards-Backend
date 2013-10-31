<?php
exit;
require_once("/var/www/lib/functions.php");
$country="US";
db::exec("update offers set active=0 where platform='dark'");
         $everbadge="http://api.everbadge.com/offersapi/offers/json?api_key=9B8yxsmXx7xv7ujVFYJNf1373448697&os=ios&country_code=$country&t=".time();
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, $everbadge);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $badgeStr=curl_exec($ch);
        $everbadgeOffers = json_decode($badgeStr,1);
        curl_close($ch);
        $data=array("rows"=>$everbadgeOffers, "ttl"=>time()+60*10);
 $everbadgeOffers=$data['rows'];
 $et=$everbadgeOffers['data']['offers'];
 foreach($et as $row){
  $off['OfferType']="App";
  $off['Action']="Share a Screenshot of this app";
  $preview=explode("id",$row['preview_url']);
  if(!isset($preview[1]) || intval($preview[1])==0) continue;
  $click=$row['offer_url']."&aff_sub=SUBID_HERE";
  $thumb=$row['thumbnail_url'];
  $off['hint']="Free App";
  $name=$row['public_name'];
  $name=addslashes($name);
  $payout=$row['payout']*100;
  $cashvalue=intval($row['payout']*20);
  $network='everbadge';
  $storeid=$preview[1];
  $active=1;
  $exist=db::row("select id from offers where affiliate_network='everbadge' and storeID=$storeid");
  if($exist){
     echo "\nupdate offers set payout=$payout, cash_value=$cashvalue,active=1 where id=".$exist['id'];
    db::exec("update offers set payout=$payout, cash_value=$cashvalue,active=1 where id=".$exist['id']);
  }else{
    echo "\ninsert into offers set name='$name', payout=$payout, cash_value=$cashvalue,click_url='$click',thumbnail='$thumb',affiliate_network='everbadge',platform='dark',active=1,storeID=$storeid";
    db::exec("insert into offers set name='$name', payout=$payout, cash_value=$cashvalue,click_url='$click',thumbnail='$thumb',affiliate_network='everbadge',platform='dark',active=1,storeID=$storeid");
  }
 }

