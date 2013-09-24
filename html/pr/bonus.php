<?php
require_once("/var/www/lib/functions.php");
require_once("/var/www/lib/firewall.php");
require_once("/var/www/html/pr/levels.php");
require_once("/var/www/html/pr/apns.php");
$uid=intval($_REQUEST['uid']);
$code=stripslashes($_REQUEST['code']);
$code=str_replace(' ','',$code);
$agent=db::row("select * from appuser where username='$code'");
$banned=0;

error_log("ddddd".json_encode($_REQUEST));

$idfa=$_REQUEST['idfa'];
$mac=$_REQUEST['mac'];

if($mac=='ios7device'){
// die(json_encode(array("title"=>"Sorry","msg"=>"device not yet supported")));
 $usercnt=db::row("select count(1) as cnt from appuser where app='picrewards' and idfa='$idfa'");
}else{
 $usercnt=db::row("select count(1) as cntfrom appuser where app='picrewards' and mac='$mac'");
}


if($usercnt['cnt']>2 && $uid!=2902){
 die(json_encode(array("title"=>"Sorry","msg"=>"Bonus code hit daily limit")));
}
$ipaddress=getRealIP();
$ipcnt=db::row("select count(1) as cnt from appuser where app='picrewards' and has_entered_bonus=1 and ipaddress='$ipaddress'");
if($ipcnt['cnt']>2){
 die(json_encode(array("title"=>"Sorry","msg"=>"Bonus code hit daily limit")));
}

if($agent['id']==9329 || $agent['id']==17035){
 die(json_encode(array("title"=>"Sorry","msg"=>"Bonus code hit daily limit")));
}

if(!$agent){
 die(json_encode(array("title"=>"Bonus Code Not Found","msg"=>"Ask your friend for a bonus code.  or search Twitter/Google for #PhotoRewards")));
}

$user=db::row("select * from appuser where id=$uid");
if($agent['banned']==1 || $user['banned']==1){
  $banned=1;
}
error_log(json_encode($agent));
$admin=0;
if($agent['role']!=0){
 $admin=1;
}
//$admin=0;
if($user['stars']<100 && $admin==0){
     die(json_encode(array("title"=>"Sorry!!","msg"=>"You must have at least 100 Points before you can enter a bonus code! Upload some pictures first")));
}
$usercode=$user['username'];
$joinername=$usercode;
if($user['has_entered_bonus']==1){
 die(json_encode(array("title"=>"Oh no!","msg"=>"You had already entered a bonus code.\n\nTell your friends to enter your bonus code '$usercode' for even more points!")));
}

$agentXpinfo=getBonusPoints($agent['xp'],$user['country']);
$min=$agentXpinfo['minbonus'];
$max=$agentXpinfo['maxbonus'];
$bonus=$min;
for(;$bonus<=$max;$bonus++){
  if(rand(0,10)<2) break; 
}
$agentUid=$agent['id'];
$joinerUid=$user['id'];
$joinerNickname=$user['username'];
$arefs=db::row("select count(distinct(ipAddress)) as ips, avg(ltv) as avgltv, count(1) as cnt from appuser a join referral_bonuses b on a.id=b.joinerUid where b.agentUid=$agentUid");
error_log("select count(distinct(ipAddress)) as ips, avg(ltv), count(1) as cnt from appuser a join referral_bonuses b on a.id=b.joinerUid where b.agentUid=$agentUid");
$distIps=$arefs['ips'];

$joiners=$arefs['cnt'];
$points_to_joineer=intval($bonus/3);
$ratio=(double)$joiners/$distIps;
error_log("ratio   $ratio");
if($joiners>2 && $ratio>1.3 && $arefs['avgltv']<100){
  die(json_encode(array("title"=>"Sorry","msg"=>"This bonus code hit daily limit")));
}
if($joiners>4 && $arefs['avgltv']<50){
  die(json_encode(array("title"=>"Sorry","msg"=>"This bonus code hit daily limit")));
}

if($ratio>4){
  error_log("bad ratio ".$code);
  db::exec("update appuser set banned=1 where id=$agentUid");
  die(json_encode(array("title"=>"Sorry","msg"=>"This bonus code hit daily limit")));
}

if($agentUid==$joinerUid){
 die(json_encode(array("title"=>"Nope!","msg"=>"You cannot enter your own bonus code! Ask your friends!")));
}

if($banned==0){
 db::exec("update appuser set stars=stars+".$bonus." where id=$agentUid");
 db::exec("update appuser set stars=stars+".$points_to_joineer.", has_entered_bonus=1 where id=$joinerUid");
 db::exec("insert into referral_bonuses set created=now(), agentUid=$agentUid, joinerUid=$joinerUid, points_to_agent=$bonus, points_to_joiner=$points_to_joineer");
 error_log("insert into referral_bonuses set created=now(), agentUid=$agentUid, joinerUid=$joinerUid, points_to_agent=$bonus, points_to_joiner=$points_to_joineer");
}else{
  error_log("not rewarding for banned agent ".json_encode($agent)." user:".json_encode($user));
}

if($joinerUid==2902){
 db::exec("update appuser set has_entered_bonus=0 where id=2902 limit 1");
}
$usercode=$user['username'];
apnsUser($agentUid,"$joinerNickname entered your bonus code '$code' for $bonus points!","");
die(json_encode(array("title"=>"You win!","msg"=>"You received $points_to_joineer extra points for entering the bonus code '$code'.\n\nTell your friends to enter your bonus code '$usercode' for even more points!")));

