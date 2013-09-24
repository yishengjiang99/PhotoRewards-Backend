<?php
require_once("/var/www/lib/functions.php");
require_once("/var/www/html/pr/apns.php"); 
$_GET=$_REQUEST;
$to=intval($_GET['toUid']);
$from =intval($_GET['from']);

if($from==14297 || $from==14266){
     die(json_encode(array("title"=>"Not sent","msg"=>"Your msg was spam")));

}
$uid=$from;
$msg=stripslashes($_GET['msg']);
if($msg==""){
     die(json_encode(array("title"=>"Not sent","msg"=>"Your msg was blank")));
}
 $msg=trim($msg);
 $msg=strtolower($msg);
$replyTo=intval($_GET['replyto']);
$tousername="";
if($replyTo==5){
   preg_match("/@([a-zA-Z]+)/",$msg,$m);
  error_log(json_encode($m));
  if(isset($m[1])){
    $username=$m[1];
    $touser=db::row("select * from appuser where username='$username'");
    if(!$touser){
       die(json_encode(array("title"=>"***The PIGEON Dood***","msg"=>"$username is not found :(")));
    } 
    $to=$touser['id'];
    $tousername=" to @".$username;
  }else{
    die(json_encode(array("title"=>"***The PIGEON Dood***","msg"=>"Add an @ in front a username to send a msg to that username")));
  }
}

if($msg=="delete" || $msg=="Delete"){
  db::exec("update inbox set readmsg=2 where id=$replyTo limit 1");  
  error_log("update inbox set readmsg=2 where id=$replyTo and to_uid=$to limit 1");
  die(json_encode(array("title"=>"***MSG MACHINE***","msg"=>"Msg Deleted")));
}
if($replyTo==2) $msg="addfriend";
if($replyTo==3 || $msg=="spin"){
  $msg=trim($msg);
  $msg=strtolower($msg);
  $reels="";
  if($msg=='terms' || $msg=='term'){
    die(json_encode(array("title"=>"***SLOT MACHINE***","msg"=>"\nNo purchase necessary.\nVoid where prohibited.\nOdds of winning is 1/9 for everyone.\nYou win when any of the 3 same items line up on the same row.\nFREE TO PLAY.\nPhotoRewards and GrepAwk LLC are the sole sponsors of this Slot Machine\nLimit 10 spins per user per day\nSpins reset at 10am PST everyday.")));
  }
  if($msg=='spin' || $msg=='s'){
     $win=0; $wint='';
     $user= db::row("select ltv,banned,tracking from appuser where id=$from");
     if($user['tracking']>=10 && $uid!=2902) die(json_encode(array("title"=>"***SLOT MACHINE***","msg"=>"You have no more spins today; come back tomorrow!")));
     if($user['ltv']<150 || $user['banned']==1) die(json_encode(array("title"=>"***SLOT MACHINE***","msg"=>"slot machine is broken!")));
     $spinleft=10-$user['tracking']-1;
     for($i=1;$i<4;$i++){
       $row="";
	$rwin=0;
       $lastJ=0;
       for($j=0;$j<3;$j++){
	 if($j==1 && rand(0,2)==0){
		$r=$lastJ;
       	}
	 else {
		$r=rand(1,3);
	}
         $lastJ=$r;
	 if($r==3) $r="$";
	 $row.="[$r]";
       }
       if($row=="[1][1][1]") $rwin=30;
       if($row=="[2][2][2]") $rwin=70;
       if($row=="[$][$][$]") $rwin=240;
       if($rwin>0) $wint.="\n[You won $rwin XP on row $i]";
       $win+=$rwin; $reels.="\n$row";
     }
     if($win>0){ 
 	db::exec("update appuser set xp=xp+$win where id=$uid");
	$user=db::row("select * from appuser where id=$uid");
	$xp=$user['xp'];
        $wint.="\nYou now have $xp Experience Points";
     }else{
	$wint="\n[Sorry no winning combinations]";
     }
     db::exec("insert into spins set uid=$uid, created=now(),win=$win");
      db::exec("update appuser set tracking=tracking+1 where id=$uid");
     die(json_encode(array("title"=>"***FREE SLOT MACHINE***","msg"=>"You pull the lever...\nreels start spinning...".$reels."$wint"."\n$spinleft Spins left for today")));
  }
  die(json_encode(array("title"=>"***FREE SLOT MACHINE***","msg"=>"Did out understand the msg '$msg'. Reply 'spin' to play!")));
}

$fromname="";
$user= db::row("select username from appuser where id=$from");
$fromname=$user['username'];
if(stripos($msg,$fromname)!==FALSE){
     die(json_encode(array("title"=>"You lose","msg"=>"Please dont spam")));
}

$msg=urlencode($msg);
db::exec("insert into inbox set from_uid=$from, to_uid=$to, msg='$msg', reply_to=$replyTo, created=now()");
error_log("insert into inbox set from_uid=$from, to_uid=$to, msg='$msg', reply_to=$replyTo, created=now()");

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
if($to==2902){
  $amsg="$fromname says '".urldecode($msg)."'";
  apnsUser($to,$amsg,"$fromname says '".urldecode($msg)."'");
}else{
 apnsUser($to,$amsg,"$fromname says '".urldecode($msg)."'");
}

if($to==2902){
     die(json_encode(array("title"=>"done","msg"=>"msg sent!\n If I don't reply within 2 hours please email yisheng@grepawk.com or txt 1-650-804-6836")));
}else{
   die(json_encode(array("title"=>"done","msg"=>"msg sent $tousername!\nReply 'delete' to any msg to delete")));
}
