<?php
require_once("/var/www/lib/functions.php");
require_once("/var/www/html/pr/apns.php");

$amount=$_GET['amount'];
if(isset($_GET['amount_cents'])){
 $amount=(double)$_GET['amount_cents']/100;
}
$network=$_GET['network'];
$ip="";
if($network=="sponsorpay"){
  $uid=intval($_GET['uid']);
  $payoutToUser=intval($_GET['amount']);
  $rev=$payoutToUser*1000/3000;
  $subid=$uid.time();
  $transID=$_GET['_trans_id_'];
  $e=db::row("select * from sponsored_app_installs where transactionID='$transID'");
  if($e){
    die();
  }
  $offerID=$_GET['lpid'];
  $app=null;
  $offerapp=db::row("select * from extapps where offer_id='$offerID'");
  if($offerapp && $offerapp['appid']!=444){
    $storeID=$offerapp['appid'];
    $up=0;
    $sameAppUser=db::row("select * from sponsored_app_installs where appid=$storeID and uid=$uid");
    if($sameAppUser){
      $payoutToUser=0; $up=0;
    }
    $app=db::row("select * from apps where id=$storeID");
  }else{
   $storeID=123456;
   $offerID="";
   $up=1;
  }
  $s2="";
    
  $insert1="insert ignore into sponsored_app_installs set Amount=$payoutToUser, appid=$storeID, uid=$uid, network='$network', offer_id='$offerID', subid='$subid',";
  $insert1.=" uploaded_picture=$up, created=now(),transactionID='$transID',revenue=$rev,sub2='$s2'";
  db::exec($insert1);
  if($up==1){
   db::exec("update appuser set stars=stars+$payoutToUser where id=$uid");
  }
  db::exec("update appuser set ltv=ltv+$rev where id=$uid");
  if($up==0 && $app){
     $name=$app['Name'];
     $msg="Thanks for trying $name! Share a screenshot for $payoutToUser Points";
  }else{
   $user=db::row("select * from appuser where id=$uid");
   $newpoints=$user['stars'];
   $msg="You got $payoutToUser new Points! New Balance: $newpoints total.";
  }
  apnsUser($uid,$msg,"");
  error_log("SPA: ".$msg."  ".strlen($msg));
  die(); 
}
$channel="";
if($network=='clicksmob' && getRealIP()!='54.243.133.243'){
// error_log("BAD IP CALLBACK ".json_encode($_GET));
}
if($network=='mobpartner' && getRealIP()!='95.131.138.180'){
 error_log("BAD IP CALLBACK ".json_encode($_GET));
}
$clientIp='';
if(isset($_GET['ip'])){
 $clientIp=$_GET['ip'];
}

$subid=$_GET['subid'];
if(strpos($subid,",")!==false){
 $st=explode(",",$subid);
}else{
 $st=explode("_",$subid);
}
$uid=intval($st[0]);
$offerID=$st[1];
$transID=$_GET['transactionID'];

if(!$transID){
// $transID=$subid;
}

$user=db::row("select * from appuser where id=$uid");
$e=db::row("select * from sponsored_app_installs where transactionID='$transID'");
if($e && $transID!=''){
  error_log("repeat transid..".json_encode($_GET));
  $response=$transID.":OK";
  die($response);
}

if(isset($_GET['sampleid'])){
  $network='admobix';
}
$s2="";
if(isset($st[2])){
 $s2=$st[2];
}
$upload=0;
$instantpay=0;
if($network!='virool' && $network!='everbadge' && $network!='aarki' && $network!='supersonic'){
 $offer=db::row("select * from offers where id=$offerID");
 if(!$offer) die("1");
 $storeID=$offer['storeID'];
 if($storeID==''){
   $storeID=$offerID;
 }
 $name=$offer['name'];
 $payoutToUser=intval($offer['cash_value'])*10;
 if($amount==0){
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
 $offer=db::row("select * from apps where id=$offerID");
 if(!$offer){
    $offer=json_decode(file_get_contents("http://json999.com/appmeta.php?appid=$offerID"),1);
 }
 if(!$offer) die("nt");
 $storeID=$offer['id']; 
 $name=$offer['Name'];
 $payoutToUser=doubleval($amount)*250;
}else if($network=='aarki'){
   $requestIp=$_GET['click_label'];
   $completionIp=$_GET['ip'];
   $ip=$completionIp;
   if($user['country']=="GB") $payoutToUser=0;
   if($requestIp!=$completionIp){
        error_log("ips don't match setting payout to 0");
	$payoutToUser=0;
   }
   $sig=$_GET['sig'];
   $payoutToUser=$_GET['puser'];
   if($payoutToUser>500){
//       $payoutToUser=0;
   }
   $e=db::row("select * from sponsored_app_installs where subid='$subid'");
   if($e){
     $payoutToUser=0;  
   }
  // if($country!="US" || in_array($user['locale'],array("","en_GB","es_LA","it_IT","vi_VN"))) $payoutToUser=$payoutToUser/3;
   $name=$_GET['name'];
   $storeID=999;
   $s2=$name;
   $storeApp=db::row("select * from extapps where offer_id='$offerID'");
   if($storeApp){
     $storeID=$storeApp['appid'];
     $upload=1;
   }
}else if($network=='supersonic'){
 $payoutToUser=$_GET['amount'];
 $recent=db::row("select count(1) as cnt from sponsored_app_installs where uid=$uid and created>date_sub(now(), interval 5 minute)");
 $rr=$recent['cnt'];
 if($uid>80000 && $rr>3){
   $payoutToUser=0;
 }
  if($user['fbid']==0 || $user['fbfriends']<3 || $user['country']=="GB" || in_array($user['locale'],array("vi_VN","en_GB","es_LA","it_IT","vi_VN"))) $payoutToUser=$payoutToUser/2;	

  $pubsubt=explode("_",$_GET['publisherSubId']);
  $ip=$pubsubt[0];
  if(isset($pubsubt[1])){
    $channel=$pubsubt[1];
  }
  if($ip!="") $ip=long2ip(intval($ip));
  $amount=$_GET['commission'];
  $storeID=444;
  $s2=htmlspecialchars_decode($_GET['offerTitle']);
  $name=$s2;
  $offerID=$s2;
  $offerapp=db::row("select * from extapps where offer_id like '%$offerID%' and network='ssa'");
  $app=null;
  $upload=0;
  if($offerapp){
     $storeID=$offerapp['appid'];
     $sameAppUser=db::row("select * from sponsored_app_installs where appid=$storeID and uid=$uid");
     if($sameAppUser){
       $payoutToUser=0; $upload=0;
     }
    $app=db::row("select * from apps where id=$storeID");
    if(!$app){
        $app=json_decode(file_get_contents("http://json999.com/appmeta.php?appid=".$_GET['offerTitle']),1);
    }
    $name=$app['Name'];
    if($app){
      $upload=1;
    }
  }
}


$amount=number_format($amount,4);
$rev=$amount*100;
$payoutToUser=intval($payoutToUser);
if($santa=db::row("select * from sponsored_app_installs where uid=$uid and appid=$storeID and network='santa'")){
 $installid=$santa['id'];
 db::exec("update sponsored_app_installs set Amount=$payoutToUser, network='$network', subid='$subid', created=now(), transactionID='$transID',revenue=$rev,offer_id=$offerID where id=$installid");
 db::exec("update UploadPictures set type='DoneApp' where refId='$installid' limit 1");
 db::exec("update appuser set stars=stars+$payoutToUser,ltv=ltv+$rev where id=$uid");
 apnsUser($uid,"You earned $payoutToUser Points for the $name screenshot!","Your earned $payoutToUser Points for the $name screenshot!");
 exit;
}

db::exec("update appuser set ltv=ltv+$rev where id=$uid");
$insert1="insert ignore into sponsored_app_installs set Amount=$payoutToUser, appid=$storeID, uid=$uid, network='$network', offer_id='$offerID', subid='$subid', created=now(),transactionID='$transID',channel='$channel',revenue=$rev,sub2='$s2',ip='$ip'";
db::exec($insert1);
$installId=db::lastID();
$name=implode(' ', array_slice(explode(' ', $name), 0, 4));

if($network=='virool'){
 $msg="You got $payoutToUser Points! Click Next for another video!";
 db::exec("update appuser set stars=stars+$payoutToUser where id=$uid");
 db::exec("update sponsored_app_installs set uploaded_picture=1 where id=$transid");
}else if($instantpay==1){
   db::exec("update appuser set stars=stars+$payoutToUser where id=$uid");
   $msg="You got $payoutToUser Points for $name!";
   db::exec("update sponsored_app_installs set uploaded_picture=1 where id=$installId");
}else if($network=="supersonic"){
  if($upload==0){
    db::exec("update appuser set stars=stars+$payoutToUser where id=$uid");
    $user=db::row("select * from appuser where id=$uid");
    $newpoints=$user['stars'];
    $msg="You got $payoutToUser Points! New Balance: $newpoints total.";
    db::exec("update sponsored_app_installs set uploaded_picture=1 where id=$installId");
  }else{
     $msg="Thanks for trying $name! Share a screenshot for $payoutToUser Points";  
  }
  if($payoutToUser>0 && $msg!="") apnsUser($uid,$msg,"");
  $response=$transID.":OK";
  die($response);
}else if($network=='aarki'){
  if($upload==0){
    db::exec("update appuser set stars=stars+$payoutToUser where id=$uid");
    $user=db::row("select * from appuser where id=$uid");
    $newpoints=$user['stars'];
    $msg="You got $payoutToUser Points! New Balance: $newpoints total.";
    db::exec("update sponsored_app_installs set uploaded_picture=1 where id=$installId");
  }else{
    $msg="Thanks for trying $name! Share a screenshot for $payoutToUser Points";
  }
}else if($sub2!=''){
  db::exec("update appuser set stars=stars+$payoutToUser where id=$uid");
  $msg="You got $payoutToUser when your friend installed $name!";
}
else{
  $msg="Thanks for trying $name! Share a screenshot for $payoutToUser Points";
}
if($msg!="" && $payoutToUser>0) apnsUser($uid,$msg,"");
echo 1;
