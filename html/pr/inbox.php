<?php
require_once("/var/www/lib/functions.php");
require_once("/var/www/lib/firewall.php"); 
$uid=intval($_GET['uid']);
$user=db::row("select * from appuser where id=$uid");
$ret=array();
$showslot=$user['tracking']<11;
$showslot=0;

$slot=array("From"=>"The Slot Machine","msg"=>"reply 'spin' to play Slots!\nNo purchase necessary. Play 10 times a day FREE. Reply 'terms' for Terms and Conditions.","msg_id"=>3,"from_uid"=>2902,"readmsg"=>!$showslot);
if($user['ltv']>10) $ret[]=$slot;

$pigeon=array("From"=>"The Pigeon Machine","msg"=>"put @username to msg anyone!","readmsg"=>1,"from_uid"=>2902,"msg_id"=>5);
if($user['visit_count']>10) $ret[]=$pigeon;

if($user['visit_count']>10 && $user['visit_count']<100){
 require_once("/var/www/html/pr/levels.php");
 $agentXpinfo=getBonusPoints($user['xp'],$user['country']);
 $url="http://photorewards.net/".$user['username'];
 $min=$agentXpinfo['minbonus'];
 $max=$agentXpinfo['maxbonus'];
 $msg="Invite a friend with the direct link: \n$url\n Or enter bonus code '".$user['username']."'\n\nEarn $min to $max Points as soon as joiners accumulate 100 points. Earn more XP to make your bonus code more powerful.";
 $news=array("From"=>"***New feature***","msg"=>$msg, "readmsg"=>1,"from_uid"=>2902,"msg_id"=>6);
 $ret[]=$news;
}

if($user['ltv']<120){
 $newbie=$user['visit_count']<5;
 $ret[]=array("From"=>"How it works","msg"=>"***How it works****\n1.Click on a picture topic from 'Earn points'.\n2. Learn more about the topic by trying it out. \n3.Take a screenshot from within the app.\n4.It can take up to 2 hours to confirm your eligibility. We will send u a notification asap. \n5.Share the screenshot on PhotoRewards to earn points",
 "readmsg"=>!$newbie,"from_uid"=>2902,"msg_id"=>10);
}

$limit=20;
if($uid==2902) $limit=20;
$friendcount=0;
$inbox=db::rows("select a.*,b.username,ltv, b.created from inbox a join appuser b on a.from_uid = b.id where to_uid=$uid and readmsg!=2 order by a.created desc limit $limit");
foreach($inbox as $m){
 $fromname="";
 $fromname=$m['username'];
 $msg=urldecode($m['msg']);
 if($msg=='addfriend'){
   $friendcount++;
   if($friendcount>2) continue;
 }
// if($uid==2902) $msg=" ".$m['ltv']." ".$m['from_uid'].": ".$msg;
 if(stripos($msg, 'addfriend')!==FALSE){
    $msg="$fromname added you as a friend. Reply 'addfriend' to add back";
 }
 if($m['readmsg']==1 && stripos($msg, 'addfriend')!==FALSE){
   continue;
 }
 $box=array("From"=>$fromname,"msg"=>$msg,"from_uid"=>$m["from_uid"],"msg_id"=>$m['id'],'readmsg'=>$m['readmsg']);
 if($m['fbid']) $box['fbid']=$m['fbid'];
 $ret[]=$box;
}
 $box=array("From"=>"SuperAdmin","msg"=>"Welcome to PhotoRewards! Message me or txt 650-804-6836 if you have any questions!","from_uid"=>2902,"msg_id"=>1,"readmsg"=>1);
 $ret[]=$box;

if($user['ltv']>0){
 $friends=db::rows("select id, username from appuser where ltv>200 and modified<date_sub(now(), interval 5 day) order by RAND() limit 1");
 foreach($friends as $f){
   $sf=array("From"=>$f['username'],"msg"=>"(Suggested Friend) Message this user to add him as a friend (30 XP)","msg_id"=>2, "from_uid"=>$f['id'],"readmsg"=>1);
   $ret[]=$sf;
 }
}
die(json_encode($ret));
