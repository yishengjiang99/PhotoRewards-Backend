<?php
$cmd='curl -L -c /var/www/tools/cookies.txt -d "api_email=ray@grepawk.com&api_password=Cc\$ec25j" http://clicksmob.com/analytics-new-api/login_api -X POST';
exec($cmd);

$cmd='curl -L -b /var/www/tools/cookies.txt http://clicksmob.com/analytics-new-api/offers.json > /var/www/tools/listclicksmoboffers.json';
exec($cmd);

$offers=json_decode(file_get_contents("/var/www/tools/listclicksmoboffers.json"),1);
//var_dump($offers);


foreach($offers as $o){
$name=$o['name'];
$end=$o['date_end'];
$affid=$o['id'];
$targets=$o['targets'];
$payouts=$o['payouts'];
foreach($payouts as $i=>$pout){
 $countries=$pout['countryNames'];
 $payout=$pout['payout'];
 $platform=$pout['platformNames'];
 $target=$targets[$i];
 $url=$target['targetUrl'];
// echo "\n$affid,$name,(".$payout."),$platform, $url";
 $preview=$target['iphonePreviewUrl'];
 $t1=explode('?',$preview);
 $t2=explode('id',$t1[0]);
 if(isset($t2[1])){
  $appstoreId=$t2[1];
   echo "\n$affid,$name,$appstoreId (".$payout."),$platform, $countries $url";
 }  
}
}
