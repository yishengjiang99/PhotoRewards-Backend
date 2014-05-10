<?php
require_once("/var/www/lib/functions.php");
require_once("/var/www/lib/firewall.php");
require_once("/var/www/html/pr/levels.php");
require_once("/var/www/html/pr/apns.php");
$interval=0;
$uid=intval($_REQUEST['uid']);
$interval=intval($_REQUEST['interval']);
$user=db::row("select * from appuser where id=$uid");
if($interval!=1 && ($user['fbid']==0 || $user['fbfriends']==0)){
  die(json_encode(array("title"=>"","msg"=>"Please login with Facebook before entering a bonus code")));
}

if($interval!=1){
 $fbid=$user['fbid'];
 $fbcnt=db::row("select count(1) as cnt from appuser where fbid=$fbid and has_entered_bonus=1");
 $fbcnt=$fbcnt['cnt'];
 if($fbcnt>1){
     die(json_encode(array("title"=>"","msg"=>"You had entered a bonus code from another account with the same Facebook account")));
 }
}

if($interval!=1 && $user['fbfriends']<1){
  die(json_encode(array("title"=>"","msg"=>"Not a verified fb account.")));
}

$code=stripslashes($_REQUEST['code']);
$code=str_replace(' ','',$code);
$agent=db::row("select * from appuser where username='$code'");
$banned=0;
error_log("ddddd".json_encode($_REQUEST));
$idfa=$_REQUEST['idfa'];
$mac=$_REQUEST['mac'];
if($mac=='ios7device'){
 $usercnt=db::row("select count(1) as cnt from appuser where app='picrewards' and idfa='$idfa'");
}else{
//  die(json_encode(array("title"=>"Sorry","msg"=>"Please upgrade to iOS 7")));
 $usercnt=db::row("select count(1) as cnt from appuser where app='picrewards' and mac='$mac'");
}

if($usercnt['cnt']>4 && $uid!=2902){
 die(json_encode(array("title"=>"Sorry","msg"=>"Bonus code hit daily limit")));
}

$tokens=db::row("select * from pushtoken where uid=$uid");
if(!$token){
// die(json_encode(array("title"=>"Sorry","msg"=>"Bonus code hit daily limit..")));
}

$ipaddress=getRealIP();
$ipcnt=db::row("select count(1) as cnt from appuser where app='picrewards' and has_entered_bonus=1 and ipaddress='$ipaddress'");
if($ipcnt['cnt']>5){
 die(json_encode(array("title"=>"Sorry","msg"=>"Bonus code hit daily limit")));
}

if($agent['id']==9329 || $agent['id']==17035 || $user['locale']=='vi_VN'){
 die(json_encode(array("title"=>"Sorry","msg"=>"Bonus code hit daily limit")));
}

if(!$agent){
 die(json_encode(array("title"=>"Bonus Code Not Found","msg"=>"Ask your friend for a bonus code.  or search Twitter/Google for #PhotoRewards")));
}

if($user['inviter_id']!=0 && $user['inviter_id']!=$agent['id'] && $uid!=2902){
 $agentId=$user['inviter_id'];
 $agent=db::row("select * from appuser where id=$agentId");
 $code=$agent['username'];
}

if($agent['banned']==1 || $user['banned']==1 || $user['banned']==2){
  $banned=1;
}

error_log(json_encode($agent));
$reached100=1;
$append="";
if($user['stars']<100 && $admin==0 && $user['ltv']<50){
 $reached100=0;
 die(json_encode(array("title"=>"","msg"=>"You must accumulate at least 100 points to enter a bonus code")));
// $append="\n\n'".$code."' will receive his bonus automatically after you have accumulated 100 additional points\n";
}

$usercode=$user['username'];
$deviceInfo=$user['deviceInfo'];
if($deviceInfo=='iPod; |5_1_1' || $deviceInfo=='iPod; |5_0_1'){
   die(json_encode(array("title"=>"Oh no!","msg"=>"Device not eligible")));
}

$device="iphone";
if(stripos($deviceInfo,"ipod")!==false){
  // die(json_encode(array("title"=>"Oh no!","msg"=>"Device not eligible")));
 $device='ipod';
}
if(stripos($deviceInfo,"ipad")!==false){
 $device='ipad';
}

$joinername=$usercode;
if($user['has_entered_bonus']>0){
 die(json_encode(array("title"=>"Oh no!","msg"=>"You had already entered a bonus code.\n\nTell your friends to enter your bonus code '$usercode' for even more points!")));
}

$agentXpinfo=getBonusPoints($agent['xp'],$user['country']);
$min=$agentXpinfo['minbonus'];
$max=$agentXpinfo['maxbonus'];
$bonus=$min;
for(;$bonus<=$max;$bonus++){
  if(rand(0,20)<2) break; 
}

$agentUid=$agent['id'];
$joinerUid=$user['id'];
$joinerNickname=$user['username'];

$arefs="select count(distinct(substring_index(ipAddress,'.',3))) as ips, avg(ltv) as avgltv, count(1) as cnt 
from appuser a join referral_bonuses b on a.id=b.joinerUid where b.agentUid=$agentUid and b.created>date_sub(now(), interval 2 day)";
$arefs=db::row($arefs);
$distIps=$arefs['ips'];

$joiners=$arefs['cnt'];
$points_to_joineer=intval($bonus/3);
$points_to_agent=$bonus;
$ratio=(double)$joiners/$distIps;
if($device=='ipod'){
  $bonus=intval($bonus/2); 
}
error_log("ratio $ratio");
if($joiners>2 && $ratio>1.3 && $arefs['avgltv']<100){
  die(json_encode(array("title"=>"Sorry","msg"=>"This bonus code hit daily limit")));
}
if($joiners>4 && $arefs['avgltv']<30){
  die(json_encode(array("title"=>"Sorry","msg"=>"This bonus code hit daily limit")));
}
if($joiners>13){
  die(json_encode(array("title"=>"Sorry","msg"=>"This bonus code hit daily limit")));
}

if($ratio>4){
  error_log("bad ratio ".$code);
  db::exec("update appuser set banned=1 where id=$agentUid");
  die(json_encode(array("title"=>"Sorry","msg"=>"This bonus code hit daily limit")));
}
$drefs="select count(distinct(deviceInfo)) as devicesCnt, count(1) as cnt from appuser a join referral_bonuses b on a.id=b.joinerUid where b.agentUid=$agentUid and b.created>date_sub(now(), interval 4 day)";
error_log($drefs);
$drefs=db::row($drefs);
$dratio=(double)$drefs['cnt']/$drefs['devicesCnt'];
error_log("$uid device ration $dratio");
if($dratio>3){
  error_log("BONUSFRAUD not awarding bonus for ".json_encode($drefs));

   die(json_encode(array("title"=>"Sorry","msg"=>"This bonus code hit daily limit")));
}

if($agentUid==$joinerUid){
 die(json_encode(array("title"=>"Nope!","msg"=>"You cannot enter your own bonus code! Ask your friends!")));
}

if($banned==0){
 if($reached100==1){
    db::exec("update appuser set stars=stars+".$points_to_agent." where id=$agentUid");
    $entered_bonus=1; 
 }else{
   $entered_bonus=2;
   $points_to_agent=0;
 }
 error_log("update appuser set stars=stars+".$points_to_joineer.", has_entered_bonus=$entered_bonus, inviter_id=$agentUid where id=$joinerUid");
 db::exec("update appuser set stars=stars+".$points_to_joineer.", has_entered_bonus=$entered_bonus, inviter_id=$agentUid where id=$joinerUid");
 db::exec("insert into referral_bonuses set created=now(), agentUid=$agentUid, joinerUid=$joinerUid, points_to_agent=$points_to_agent, points_to_joiner=$points_to_joineer");
}else{
  error_log("not rewarding for banned agent ".json_encode($agent)." user:".json_encode($user));
}

if($joinerUid==2902 || $joinerUid==29184){
 db::exec("update appuser set has_entered_bonus=0, inviter_id=0 where id=$joinerUid limit 1");
}

$usercode=$user['username'];
 if($reached100==1 && $banned==0) {
    apnsUser($agentUid,"$joinerNickname entered your bonus code '$code' for $bonus points!","");
 }

die(json_encode(array("title"=>"You win!","msg"=>"You received $points_to_joineer extra points for entering the bonus code '$code'.$append\nTell your friends to enter your bonus code '$usercode' for even more points!")));

