<?php
require_once("/var/www/lib/functions.php");
require_once("/var/www/html/pr/apns.php"); 
$_GET=$_REQUEST;
$to=intval($_GET['toUid']);
$from =intval($_GET['from']);
$uid=$from;
$msg=stripslashes($_GET['msg']);
if($msg==""){
     die(json_encode(array("title"=>"Not sent","msg"=>"Your msg was blank")));
}
$msg=urlencode($msg);
$replyTo=intval($_GET['replyto']);

if($replyTo==2) $msg="addfriend";

if($replyTo==3){
  $msg=trim(strtolower($msg));
  $reels="";
  if($msg=='spin'){
     for($i=0;$i<3;$i++){
       $reels.="\n";
       for($j=0;$j<3;$j++){
	$reels.="[".rand(0,3)."]";
       }
     }
     die(json_encode(array("title"=>"***SLOT MACHINE***","msg"=>"You pull the lever...\nreels start spinning...\n...\n".$reels)));
  }
//  die(json_encode(array("title"=>"***SLOT MACHINE***","msg"=>"You pull the lever and reels start spinning....\n\n[#][3][5]\n[@][4][3]\n[5][5][5]\n\n***YOU WON***")));
}
db::exec("insert into inbox set from_uid=$from, to_uid=$to, msg='$msg', reply_to=$replyTo, created=now()");
error_log("insert into inbox set from_uid=$from, to_uid=$to, msg='$msg', reply_to=$replyTo, created=now()");

$fromname="";
$user= db::row("select username from appuser where id=$from");
$fromname=$user['username'];
if(stripos($msg,$fromname)!==FALSE){
     die(json_encode(array("title"=>"You lose","msg"=>"Please dont spam")));
}

$amsg="$fromname sent you a message!";
if(stripos($msg, 'addfriend')!==FALSE){
  $cnt=db::row("select count(1) as cnt from friends where f1=$from and created>date_sub(now(), interval 2 minute)");
  $cnt=$cnt['cnt'];
  if($cnt>1){
    die(json_encode(array("title"=>"Pwned","msg"=>"Don't spam!")));
  }
  $existing=db::row("select * from friends where f1=$from and f2=$to");
  if(!$existing){
   $cnt=db::row("select count(1) as cnt from friends where f1=$from");
   $cnt=$cnt['cnt'];
   if($cnt>10){
    die(json_encode(array("title"=>"done","msg"=>"You are capped to 10 friends (fow now)")));
   }
   $friend=db::row("select username from appuser where id=$to");
   $friendname=$friend['username'];
   $xp=30;$e="friended $friendname";
   db::exec("insert into friends set f1=$from, f2=$to, created=now()");
   db::exec("insert into pr_xp set uid=$from,xp=$xp,created=now(),event='$e'");
   db::exec("update appuser set xp=xp+$xp where id=$from");
   $amsg="$fromname added you as a friend!";
   $imsg="reply 'addfriend' to friend back";
   apnsUser($to,$amsg,$imsg);   
   die(json_encode(array("title"=>"done","msg"=>"$friendname has been added as your friend for 30 XP!")));
 }else{
    die(json_encode(array("title"=>"done","msg"=>"You already friended him/her before.")));
 }
}

apnsUser($to,$amsg,"$fromname says '".urldecode($msg)."'");
die(json_encode(array("title"=>"done","msg"=>"msg sent!")));

