<?php
require_once("/var/www/lib/functions.php");

//fb.php?fbid=1200402&email=yishengjiang@yahoo.com&gender=male&mac=A0:ED:CD:75:37:88&cb=slide
$fbdata=$_POST['data'];
$mac=$_POST['mac'];
$uid=$_POST['uid'];
$json=json_decode($_POST['data'],1);
$fbid=$json['id'];
$email=$json['email'];
$gender=$json['gender'];
$fname=$json['first_name'];
$row=db::row("select * from fbusers where fbid=$fbid");
if($row) die(json_encode(array("title"=>"You Win","msg"=>"Welcome $fname")));
db::exec("insert ignore into fbusers set fbid=$fbid,email='$email',gender='$gender',mac='$mac',firstname='$fname', uid=$uid,fbdata='$fbdata'");
db::exec("update appuser set fbid=$fbid, email='$email' where id=$uid");
die(json_encode(array("title"=>"You Win","msg"=>"Welcome $fname")));
