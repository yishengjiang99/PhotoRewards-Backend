<?php
require_once("/var/www/lib/functions.php");
require_once("/var/www/lib/firewall.php"); 
$uid=intval($_GET['uid']);
$user=db::row("select * from appuser where id=$uid");
$ret=array();

 $slot=array("From"=>"The Slot Machine","msg"=>"reply 'spin' to play Slots!\nNo purchase necessary. Play 10 times a day FREE. Reply 'terms' for Terms and Conditions.","msg_id"=>3,"from_uid"=>2902,"readmsg"=>0);
//if($user['ltv']>200) $ret[]=$slot;

$limit=20;
if($uid==2902) $limit=200;
$inbox=db::rows("select a.*,b.username,ltv, b.created from inbox a join appuser b on a.from_uid = b.id where to_uid=$uid order by a.created desc limit $limit");

foreach($inbox as $m){
 $fromname="";
 $fromname=$m['username'];
 $msg=urldecode($m['msg']);
 if($uid==2902) $msg=" ".$m['ltv']." ".$m['from_uid'].$msg;
 if(stripos($msg, 'addfriend')!==FALSE){
    $msg="$fromname added you as a friend. Reply 'addfriend' to add back";
 }
 $box=array("From"=>$fromname,"msg"=>$msg,"from_uid"=>$m["from_uid"],"msg_id"=>$m['id'],'readmsg'=>$m['readmsg']);
 if($m['fbid']) $box['fbid']=$m['fbid'];
 $ret[]=$box;
}
 $box=array("From"=>"SuperAdmin","msg"=>"Welcome to PhotoRewards! Message me or txt 650-804-6836 if you have any questions!","from_uid"=>2902,"msg_id"=>1,"readmsg"=>1);
 $ret[]=$box;

if($user['ltv']>0){
 $friends=db::rows("select id, username from appuser where ltv>200 and modified<date_sub(now(), interval 5 day) order by RAND() limit 2");
 foreach($friends as $f){
   $sf=array("From"=>$f['username'],"msg"=>"(Suggested Friend) Message this user to add him as a friend (30 XP)","msg_id"=>2, "from_uid"=>$f['id'],"readmsg"=>1);
   $ret[]=$sf;
 }
}



die(json_encode($ret));
