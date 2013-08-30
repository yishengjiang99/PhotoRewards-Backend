

<?php
require_once("/var/www/lib/functions.php");
require_once("/var/www/lib/firewall.php");
error_log(json_encode($_REQUEST));
$_GET=$_REQUEST;
$mac=$_GET['mac'];
$idfa=$_GET['idfa'];
$cb=$_GET['cb'];
$uid=intval($_GET['uid']);
$user=db::row("select * from appuser where id=$uid");
$ltv=$user['ltv'];
$joinedDate=$user['created'];
$pointsFromUserPictures=db::row("select sum(points_earned)/10 as t from UploadPictures where type='UserOffers' and uid=$uid");
$pointsFromUserPictures=$pointsFromUserPictures['t'];
$refP=db::row("select sum(points_to_agent)/10 as t from referral_bonuses where agentUid=$uid");
$refP=$refP['t'];
$usum="ltv=$ltv UPP=$pointsFromUserPictures refP=$refP";
if($user['banned']==1){
 die(json_encode(array("title"=>"","msg"=>"Sorry! This reward is out of stock! Check back tomorrow!")));
}
$rid=intval($_REQUEST['giftID']);
$reward=db::row("select * from rewards where id=$rid");
error_log("redeem ".$user['stars']." VS  ".$reward['Points']."   ".json_encode($user));
if($reward['Points']>$user['stars']){
 die(json_encode(array("title"=>"","msg"=>"You do not have enough points for this reward")));
}

if($reward['available']==0 && $uid!=2902 && $uid!=7885){
 die(json_encode(array("title"=>"","msg"=>"Sorry! This reward is out of stock! Check back tomorrow!")));
}

if($reward['Type']=='gc'){
 $hascode=db::row("select 1 from reward_codes where given_out=0 and reward_id=$rid limit 1");
 if(!$hascode) die(json_encode(array("title"=>"","msg"=>"Sorry! This reward is out of stock! Check back tomorrow!")));
 error_log("update reward_codes set given_out=1, date_redeemed=now(),rewarded_to_uid=$uid where given_out=0 and reward_id=$rid limit 1");
 db::exec("update reward_codes set given_out=1, date_redeemed=now(),rewarded_to_uid=$uid where given_out=0 and reward_id=$rid limit 1");
 error_log("select aes_decrypt(code,'supersimple') as code from reward_codes where reward_id=$rid, rewarded_to_uid=$uid, given_out=1 order by date_redeemed desc limit 1");
 $code=db::row("select aes_decrypt(code,'supersimple') as code from reward_codes where reward_id=$rid and rewarded_to_uid=$uid and given_out=1 order by date_redeemed desc limit 1");
 if(!$code) die(json_encode(array("title"=>"","msg"=>"Sorry! This reward is out of stock! Check back tomorrow!")));
 db::exec("update appuser set stars=stars-".$reward['Points']." where id=$uid");
 $codestr=$code['code'];
 $name=$reward['name'];
 $instruction=$reward['instruction'];
 require_once("/var/www/html/pr/apns.php");
 apnsUser(2902,"$uid redeemed $name $usum","$uid redeemed $name");
 $ret=array("title"=>"You win!","msg"=>"The $name code that you redeemed is:\n\r$codestr\n\r$instruction"); 

 if($reward['action']!=""){
   $url=$reward['action'];
   $url=str_replace("CODE_HERE",$codestr,$url);
   $ret['url']=$url;
 }  
 die(json_encode($ret));
}else if ($reward['Type']=='Paypal'){
 if(!isset($_GET['email'])){
    $h=$_GET['h'];
    $idfa=$_GET['idfa'];
    $t=$_GET['t'];
    $mac=$_GET['mac'];
    $url="https://www.json999.com/enterEmail.php?uid=$uid&h=$h&t=$t&idfa=$idfa&mac=$mac";
    die(json_encode(array("title"=>"Email required","msg"=>"****Further action required****\nClick 'GO' to enter your PayPal email address","url"=>$url)));
 }
 $email=stripslashes($_GET['email']);
 if($email=="orlando12.12@hotmail.com" || $email=="Orlando12.12@hotmail.com"){
   die(json_encode(array("title"=>"","msg"=>"Sorry! This reward is out of stock! Check back tomorrow!")));
  }
 db::exec("update appuser set email='$email' where id=$uid");
 $balance=$user['stars'];
 $value=ceil($balance/10);
 $cnt=db::cols("select count(distinct transfer_to_user_id) from PaypalTransactions where created>date_sub(now(), interval 3 day) and email='$email'");
 $cntint=$cnt[0];
 error_log("$cntint distinct user paying to $email");
 $status='init';
 if($cntint>3) $status='banned';
 db::exec("update appuser set stars=0 where id=$uid");
 $trxid=time().$uid;
 db::exec("insert into PaypalTransactions set transfer_to_user_id=$uid,email='$email',status='$status',amount='$value',masspay_trx_id=$trxid,created=now()"); 
 $cmd="php /var/www/tools/masspay.php  > /dev/null 2>&1 &";
 exec($cmd);
 require_once("/var/www/html/pr/apns.php");
 apnsUser(2902,"$uid redeemed $value in paypal: $usum","$uid redeemed $value in paypal: $usum");

 die(json_encode(array("title"=>"You win!","msg"=>"PayPal payments will be made to $email shortly.\n\nLike PhotoRewards? Please take a moment to rate us in the App Store.",'url'=>'http://itunes.apple.com/WebObjects/MZStore.woa/wa/viewContentsUserReviews?pageNumber=0&sortOrdering=1&type=Purple+Software&mt=8&id=662632957')));
}else if ($reward['Type']=="iap"){
 die(json_encode(array("title"=>"You win!","msg"=>"You have been awarded with ".$reward['name']."\nWould you like to use your reward now?","url"=>$reward['action'])));
}
