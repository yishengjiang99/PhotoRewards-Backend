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

$replyTo=intval($_GET['replyto']);

if($replyTo==2) $msg="addfriend";

if($replyTo==3){
  $msg=trim($msg);
  $msg=strtolower($msg);
  $reels="";
  if($msg=='terms' || $msg=='term'){
    die(json_encode(array("title"=>"***SLOT MACHINE***","msg"=>"\nNo purchase necessary.\nVoid where prohibited.\nOdds of winning is 1/9 for everyone.\nYou win when any of the 3 same items line up on the same row.\nFREE TO PLAY.\nPhotoRewards and GrepAwk LLC are the sole sponsors of this Slot Machine\nLimit 10 spins per user per day\nSpins reset at 10am PST everyday.")));
  }
  if($msg=='spin' || $msg=='s'){
     $win=0; $wint='';
     $user= db::row("select tracking from appuser where id=$from");
     if($user['tracking']>=10 && $uid!=2902) die(json_encode(array("title"=>"***SLOT MACHINE***","msg"=>"You have no more spins today; come back tomorrow!")));
     $spinleft=10-$user['tracking']-1;
     for($i=1;$i<4;$i++){
       $row="";
	$rwin=0;
       for($j=0;$j<3;$j++){
	 $r=rand(1,3);
	 if($r==3) $r="$";
	 $row.="[$r]";
       }
       if($row=="[1][1][1]") $rwin=3;
       if($row=="[2][2][2]") $rwin=7;
       if($row=="[$][$][$]") $rwin=24;
       if($rwin>0) $wint.="\n[You won $rwin points on row $i]";
       $win+=$rwin; $reels.="\n$row";
     }
     if($win>0){ 
 	db::exec("update appuser set stars=stars+$win where id=$uid");
	$user=db::row("select * from appuser where id=$uid");
	$points=$user['stars'];
        $wint.="\nYou now have $points Points";
     }else{
	$wint="\n[Sorry no winning combinations]";
     }
     db::exec("insert into spins set uid=$uid, created=now(),win=$win");
     db::exec("update appuser set stars=stars+$win,tracking=tracking+1 where id=$uid");
     die(json_encode(array("title"=>"***FREE SLOT MACHINE***","msg"=>"You pull the lever...\nreels start spinning...\n".$reels."\n$wint"."\n$spinleft Spins left for today")));
  }
  die(json_encode(array("title"=>"***FREE SLOT MACHINE***","msg"=>"Did out understand the msg '$msg'. Reply 'spin' to play!")));
}
$msg=urlencode($msg);
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

