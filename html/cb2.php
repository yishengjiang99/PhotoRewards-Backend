<?php
require_once("/var/www/lib/functions.php");
require_once("/var/www/html/pr/apns.php");

$amount=$_GET['amount'];
if(isset($_GET['amount_cents'])){
 $amount=(double)$_GET['amount_cents']/100;
}
$network=$_GET['network'];
$subid=$_GET['subid'];
$st=explode(",",$subid);
$uid=intval($st[0]);
$offerID=$st[1];
$transID=$_GET['transactionID'];
if(isset($_GET['sampleid'])){
  $network='admobix';
}
$instantpay=0;
if($network!='virool' && $network!='everbadge'){
 $offer=db::row("select * from offers where id=$offerID");
 if(!$offer) die("1");
 $storeID=$offer['storeID'];
 if($storeID==''){
   $storeID=$offerID;
 }
 $name=$offer['name'];
 $payoutToUser=intval($offer['cash_value'])*10;
 if($network=='clicksmob'){
   $amount=(double)$offer['payout']/100;
 }
 if($storeID<1000){
   $instantpay=1;
 }
}
else if($network=='virool'){
    $uid=intval($_GET['subid']);
    $offerID=44;
    $amount=0.01;
    $payoutToUser=5;
    $storeID=111;
 } 
else if($network=='everbadge'){
 $offer=json_decode(file_get_contents("http://json999.com/appmeta.php?appid=$offerID"),1);
 if(!$offer) die("1");
 $storeID=$offer['id']; 
 $name=$offer['Name'];
 $payoutToUser=doubleval($amount)*500;
}
if(!$transID){
 $transID=$subid;
}
$amount=number_format($amount,2);
$rev=$amount*100;
$payoutToUser=rand(intval($payoutToUser*0.5), intval($payoutToUser*1.3));
if($santa=db::row("select * from sponsored_app_installs where uid=$uid and appid=$storeID and network='santa'")){
 $installid=$santa['id'];
 db::exec("update sponsored_app_installs set Amount=$payoutToUser, network='$network', subid='$subid', created=now(), transactionID='$transID',revenue=$rev where id=$installid");
 db::exec("update appuser set stars=stars+$payoutToUser where id=$uid");
 apnsUser($uid,"Your earned $payoutToUser Points for $name screenshot!","Your earned $payoutToUser Points for $name screenshot!");
// apnsUser(2902,"Your earned $payoutToUser Points for $name screenshot!","Your earned $payoutToUser Points for $name screenshot!");
exit;
} 
$insert1="insert ignore into sponsored_app_installs set Amount=$payoutToUser, appid=$storeID, uid=$uid, network='$network', offer_id='$offerID', subid='$subid', created=now(),transactionID='$transID',revenue=$rev";
error_log($insert1);
db::exec($insert1);
$installId=db::lastID();
$insert="insert ignore into offer_completions set amount=$amount, user_id=$uid, network='$network', offer_id='$offerID', subid='$subid', created=now(),transactionID='$transID'";
db::exec($insert);
$transid=db::lastID();
if($network=='virool'){
 $msg="You got $payoutToUser Points! Click Next for another video!";
 db::exec("update appuser set stars=stars+$payoutToUser where id=$uid");
 db::exec("update sponsored_app_installs set uploaded_picture=1 where id=$transid");
}else if($instantpay==1){
   db::exec("update appuser set stars=stars+$payoutToUser where id=$uid");
   $msg="You got $payoutToUser Points for $name!";
   db::exec("update sponsored_app_installs set uploaded_picture=1 where id=$installId");
}else{
 $msg="Thanks for trying $name! Share a screenshot for $payoutToUser Points";
}
error_log($msg."  ".strlen($msg));
apnsUser($uid,$msg,"");

echo 1;


