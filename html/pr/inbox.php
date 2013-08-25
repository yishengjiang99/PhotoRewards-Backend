<?php
require_once("/var/www/lib/functions.php");

$uid=intval($_GET['uid']);
$user=db::row("select * from appuser where id=$uid");
$ret=array();

if($user['ltv']>0){
 $friends=db::rows("select id, username from appuser where ltv>0 and modified<date_sub(now(), interval 5 day) order by RAND() limit 2");

 foreach($friends as $f){
  $sf=array("From"=>$f['username'],"msg"=>"(Suggested Friend) Message this user to add him as a friend(30 XP)","msg_id"=>2, "from_uid"=>$f['id'],"readmsg"=>0);
  $ret[]=$sf;
 }
}


$inbox=db::rows("select a.*,b.fbid,b.username,c.firstname from inbox a join appuser b on a.from_uid = b.id left join fbusers c on b.fbid=c.fbid where to_uid=$uid order by created desc limit 10");
foreach($inbox as $m){
 $fromname="";

 if($m['firstname']) $fromname=$m['firstname'];
 else $fromname=$m['username'];
 
 $fromname=$m['username'];
 $msg=urldecode($m['msg']);
 if(stripos($msg, 'addfriend')!==FALSE){
    $msg="$fromname added you as a friend. Reply 'addfriend' to add back";
 }
 
 $box=array("From"=>$fromname,"msg"=>$msg,"from_uid"=>$m["from_uid"],"msg_id"=>$m['id'],'readmsg'=>$m['readmsg']);
 if($m['fbid']) $box['fbid']=$m['fbid'];
 $ret[]=$box;
}

 $box=array("From"=>"SuperAdmin","msg"=>"Welcome to PhotoRewards! Message me or txt 650-804-6836 if you have any questions!","from_uid"=>2902,"msg_id"=>1,"readmsg"=>1);

 $ret[]=$box;

die(json_encode($ret));
