<?php
require_once("/var/www/lib/functions.php");
require_once("/var/www/lib/firewall.php");

require_once("/var/www/html/pr/levels.php");
require_once("/var/www/html/pr/apns.php");
$uid=intval($_REQUEST['uid']);
$code=stripslashes($_REQUEST['code']);
$agent=db::row("select * from appuser where username='$code'");
$banned=$agent['banned'];

if($agent['id']==9329){
 die(json_encode(array("title"=>"Sorry","msg"=>"Bonus code hit daily limit")));
}

if(!$agent){
 die(json_encode(array("title"=>"Bonus Code Not Found","msg"=>"Ask your friend for a bonus code.  or search Twitter/Google for #PhotoRewards")));
}

$user=db::row("select * from appuser where id=$uid");

if($user['stars']<100 || $user['banned']==1){
 die(json_encode(array("title"=>"Sorry","msg"=>"You must have at least 100 Points before you can enter a bonus code! Upload some pictures first")));
}

$usercode=$user['username'];
$joinername=$usercode;
if($user['has_entered_bonus']==1){
 die(json_encode(array("title"=>"Oh no!","msg"=>"You had already entered a bonus code.\n\nTell your friends to enter your bonus code '$usercode' for even more points!")));
}

$agentXpinfo=getBonusPoints($agent['xp']);
$min=$agentXpinfo['minbonus'];
$max=$agentXpinfo['maxbonus'];
$bonus=$min;
for(;$bonus<=$max;$bonus++){
  if(rand(0,50)<2) break; 
}
$agentUid=$agent['id'];
$joinerUid=$user['id'];
$arefs=db::row("select count(distinct(ipAddress)) as ips, avg(ltv) as avgltv, count(1) as cnt from appuser a join referral_bonuses b on a.id=b.joinerUid where b.agentUid=$agentUid");
error_log("select count(distinct(ipAddress)) as ips, avg(ltv), count(1) as cnt from appuser a join referral_bonuses b on a.id=b.joinerUid where b.agentUid=$agentUid");
$distIps=$arefs['ips'];

$joiners=$arefs['cnt'];
$points_to_joineer=intval($bonus/3);
if($user['stars']<100 && $arefs['avgltv']<2.0){
  die(json_encode(array("title"=>"Sorry","msg"=>"You must have at least 100 Points before you can enter a bonus code! Upload some pictures first")));
}

if($joiners>2 && $joiners/$distIps>1.5 && $arefs['avgltv']<2.0 && $user['stars']<100){
  die(json_encode(array("title"=>"Sorry","msg"=>"You must have at least 100 Points before you can enter a bonus code! Upload some pictures first")));
}

if($agentUid==$joinerUid){
 die(json_encode(array("title"=>"Nope!","msg"=>"You cannot enter your own bonus code! Ask your friends!")));
}
if($banned==0){
 db::exec("update appuser set stars=stars+".$bonus." where id=$agentUid");
 db::exec("update appuser set stars=stars+".$points_to_joineer.", has_entered_bonus=1 where id=$joinerUid");
 db::exec("insert into referral_bonuses set created=now(), agentUid=$agentUid, joinerUid=$joinerUid, points_to_agent=$bonus, points_to_joiner=$points_to_joineer");
 error_log("insert into referral_bonuses set created=now(), agentUid=$agentUid, joinerUid=$joinerUid, points_to_agent=$bonus, points_to_joiner=$points_to_joineer");
}

if($joinerUid==2902){
 db::exec("update appuser set has_entered_bonus=0 where id=2902 limit 1");
}

error_log("insert into referral_bonuses set created=now(), agentUid=$agentUid, joinerUid=$joinerUid, points_to_agent=$bonus, points_to_joiner=$points_to_joineer");
$usercode=$user['username'];
apnsUser($agentUid,"Someone entered your bonus code '$code' for $bonus points!","");
die(json_encode(array("title"=>"You win!","msg"=>"You received $points_to_joineer extra points for entering the bonus code '$code'.\n\nTell your friends to enter your bonus code '$usercode' for even more points!")));

