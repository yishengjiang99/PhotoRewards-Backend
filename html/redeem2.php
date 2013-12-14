<?php
exit;
require_once("/var/www/lib/functions.php");
require_once("/var/www/lib/firewall.php");
error_log(json_encode($_REQUEST));
//$paypalon=date('H') >5 && date('H')<24;
//$paypalon=true;
$paypalon=true;
$_GET=$_REQUEST;
$mac=$_GET['mac'];
$idfa=$_GET['idfa'];
$cb=$_GET['cb'];
$uid=intval($_GET['uid']);
$user=db::row("select * from appuser where id=$uid");
if($user['country']!="US"){
//       die(json_encode(array("title"=>"","msg"=>"Sorry! This reward is out of stock! Check back tomorrow!")));
}
if($user['fbid']==0 || $user['fbfriends']==0){
 $cb="https://www.json999.com/pr/fblogin.php?uid=$uid";
 $url="https://www.facebook.com/dialog/oauth?response_type=code&client_id=146678772188121&scope=email&redirect_uri=".urlencode($cb);
// die(json_encode(array("title"=>"","msg"=>"Please login with Facebook before entering a bonus code","url"=>$url)));
}

$ltv=$user['ltv'];
$ip=implode(".",array_slice(explode(".",$user['ipAddress']),0,3)).".";
$banned=db::row("select * from bannedIps where ip='$ip'");

if($banned){
      die(json_encode(array("title"=>"","msg"=>"Sorry! This reward is out of stock! Check back tomorrow!")));
}
$joinedDate=$user['created'];
$pointsFromUserPictures=db::row("select sum(points_earned)/10 as t from UploadPictures where type='UserOffers' and uid=$uid");
$pointsFromUserPictures=$pointsFromUserPictures['t'];
$refP=db::row("select sum(points_to_agent)/10 as t from referral_bonuses where agentUid=$uid");
$refP=$refP['t'];
$usum="ltv=$ltv UPP=$pointsFromUserPictures refP=$refP";
if($user['banned']==1){
 die(json_encode(array("title"=>"","msg"=>"Your account is under review. Please email yisheng@grepawk.com with your username or facebook email")));
}
$rid=intval($_REQUEST['giftID']);
$reward=db::row("select * from rewards where id=$rid");
error_log("redeem ".$user['stars']." VS  ".$reward['Points']."   ".json_encode($user));
if($reward['Points']>$user['stars']){
 die(json_encode(array("title"=>"","msg"=>"You do not have enough points for this reward")));
}

if($reward['available']!=1 && $uid!=2902 && $uid!=7885){
 die(json_encode(array("title"=>"","msg"=>"Sorry! This reward is out of stock! Check back tomorrow!")));
}
if($reward['Type']=='gc'){
 $recent=db::row("select sum(Points) as t from reward_codes where rewarded_to_uid=$uid and date_redeemed>date_sub(now(), interval 3 hour)");
 $rt=$recent['t'];
 if($rt>4000){
     error_log("velocity break");
     die(json_encode(array("title"=>"","msg"=>"Sorry! This reward is out of stock! Check back tomorrow!")));
 }
 if($reward['requiresEmail']==1){
  $email="";
  if(isset($_GET['email'])){
    $email=$_GET['email'];
  }else{
    $h=$_GET['h'];
    $idfa=$_GET['idfa'];
    $t=$_GET['t'];
    $mac=$_GET['mac'];
    $url="https://www.json999.com/enterEmailForGC.php?rid=$rid&uid=$uid&h=$h&t=$t&idfa=$idfa&mac=$mac";
    die(json_encode(array("title"=>"Email required","msg"=>"****Further action required****\nClick 'GO' to enter your email address, which we will use to send you the gift card","url"=>$url)));
  }
  $email=stripslashes($_GET['email']);
  if(check_email_address($email)===FALSE){
       error_log("$email is not valid");
       die(json_encode(array("title"=>"","msg"=>$email." is not a valid email address.")));
  }
  $otheruids=db::row("select count(distinct uid) as cnt from gc_emails_delivery where email='$email'");
  $othercnt=$otheruids['cnt'];
  if($othercnt>2) {
    db::exec("update appuser set banned=1 where uid=$uid limit 1");
    error_log("multi accounts email redemption");
    die(json_encode(array("title"=>"","msg"=>"Sorry! This reward is out of stock! Check back tomorrow!")));
  }
 }

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
 if($email!='' && in_array($rid,array(2,3))){ //
   $template=file_get_contents("/var/www/html/agcod.html");
   $template=str_replace("DOLLAR_AMOUNT_HERE","$".$reward['CashValue'],$template);
   $template=str_replace("CARD_CODE_HERE",$codestr,$template);
   db::exec("insert into gc_emails_delivery set uid=$uid,reward_id=".$code['id'].",email='$email',delivered=1,created=now()");
   email($email,"Your Amazon.com Gift Card Code from PhotoRewards",$template,"support@photorewards.net");
 }
 if($email!='' && $rid==11){
   $codet=explode("|",$codestr);
   $urlcode=$codet[0];
   $secret=$codet[1];
   $template=file_get_contents("/var/www/html/starbucks.html");
   $template=str_replace("DOLLAR_AMOUNT_HERE","$".$reward['CashValue'],$template);
   $template=str_replace("URL_CODE_HERE",$urlcode,$template);
   $template=str_replace("SECRET_CODE_HERE",$secret,$template);
   db::exec("insert into gc_emails_delivery set uid=$uid,reward_id=".$code['id'].",email='$email',delivered=1,created=now()");
   email($email,"Your Starbucks Gift Card from PhotoRewards",$template,"support@photorewards.net");
 }
 
   
 if($reward['requiresEmail']==1){
   die(json_encode(array("title"=>"You Win","msg"=>"Your ".$reward['name']." has been sent to $email")));
 }  
 $instruction=$reward['instruction'];
 $ret=array("title"=>"You win!","msg"=>"The $name code that you redeemed is:\n\r$codestr\n\r$instruction"); 
 if($reward['action']!=""){
   $url=$reward['action'];
   $url=str_replace("CODE_HERE",$codestr,$url);
   $ret['url']=$url;
 }  
 die(json_encode($ret));
}else if ($reward['Type']=='Paypal'){
 if(!$paypalon){
    die(json_encode(array("title"=>"","msg"=>"Sorry! This reward is out of stock! Check back tomorrow!")));
 }
 if(!isset($_GET['email'])){
    $h=$_GET['h'];
    $idfa=$_GET['idfa'];
    $t=$_GET['t'];
    $mac=$_GET['mac'];
    $url="https://www.json999.com/enterEmail.php?uid=$uid&h=$h&t=$t&idfa=$idfa&mac=$mac";
    die(json_encode(array("title"=>"Email required","msg"=>"****Further action required****\nClick 'GO' to enter your PayPal email address","url"=>$url)));
 }
 $email=stripslashes($_GET['email']);
 if(check_email_address($email)===FALSE){
       error_log("$email is not valid");
       die(json_encode(array("title"=>"","msg"=>$email." is not a valid email address.")));
  }
 if($email=="jennyburge90@gmail.com" || $email=='phongthanh0234@yahoo.com' || $email=='phuongsuong0123@yahoo.com' 
  || $email=="orlando12.12@hotmail.com" || $email=="Orlando12.12@hotmail.com" || $email=="shiritrong@gmail.com" || $email=="Jamewilh22@gmail.com" || $email=="Hexuni-ction@yahoo.co.uk" || $email=='paint_peter@yahoo.com'){
   	        db::exec("update appuser set banned=1 where id=$uid");
	die(json_encode(array("title"=>"","msg"=>"Sorry! This reward is out of stock! Check back tomorrow!")));
  }

 db::exec("update appuser set email='$email' where id=$uid");
 $balance=$user['stars'];
 $value=ceil($balance/10);
 if($value>950) $value=$value+50;
 $cnt=db::cols("select count(distinct transfer_to_user_id) from PaypalTransactions where created>date_sub(now(), interval 1 day) and email='$email'");
 $cntint=$cnt[0];
 error_log("$cntint distinct user paying to $email");
 $status='init';
 if($cntint>2) {
        db::exec("update appuser set banned=1,note='$cntint users paying to same email' where id=$uid");
	die(json_encode(array("title"=>"","msg"=>"Sorry! This reward is out of stock! Check back tomorrow!")));
 }
 $recent=db::cols("select sum(amount) as total from PaypalTransactions where created>date_sub(now(), interval 1 day) and email='$email'");
 $recent=$cnt[0];
 error_log("$recent amount paied to $email");
 if($recent>1000) die(json_encode(array("title"=>"","msg"=>"Sorry! This reward is out of stock! Check back tomorrow!")));

 db::exec("update appuser set stars=0 where id=$uid");
 $trxid=time().$uid;
 db::exec("insert into PaypalTransactions set transfer_to_user_id=$uid,email='$email',status='$status',amount='$value',masspay_trx_id=$trxid,created=now()"); 
 $cmd="php /var/www/tools/masspay.php  > /dev/null 2>&1 &";
 exec($cmd);
 die(json_encode(array("title"=>"You win!","msg"=>"PayPal payments will be made to $email shortly.\n\nLike PhotoRewards? Please take a moment to rate us in the App Store.",'url'=>'http://itunes.apple.com/WebObjects/MZStore.woa/wa/viewContentsUserReviews?pageNumber=0&sortOrdering=1&type=Purple+Software&mt=8&id=662632957')));
}else if ($reward['Type']=="iap"){
 die(json_encode(array("title"=>"You win!","msg"=>"You have been awarded with ".$reward['name']."\nWould you like to use your reward now?","url"=>$reward['action'])));
}
