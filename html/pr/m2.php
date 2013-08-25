<?php
require_once("/var/www/lib/functions.php");
require_once("/var/www/html/pr/apns.php"); 
$_GET=$_REQUEST;
$to=intval($_GET['toUid']);
$from =intval($_GET['from']);
$msg=stripslashes($_GET['msg']);
$msg=urlencode($msg);
db::exec("insert into inbox set from_uid=$from, to_uid=$to, msg='$msg', created=now()");

$fromname="";
$fb=db::row("select firstname from fbusers where uid=$from");
if($fb){
  $fromname=$fb['firstname'];
}
else {
 $user= db::row("select username from appuser where id=$from");
 $fromname=$user['username'];
}

$amsg="$fromname sent you a message!";
apnsUser($to,$amsg,"$fromname says '$msg'");

die(json_encode(array("title"=>"done","msg"=>"msg sent!")));

