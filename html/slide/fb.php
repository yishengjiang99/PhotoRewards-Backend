<?php
require_once("/var/www/lib/functions.php");

//fb.php?fbid=1200402&email=yishengjiang@yahoo.com&gender=male&mac=A0:ED:CD:75:37:88&cb=slide

$fbid=$_GET['fbid'];
$email=$_GET['email'];
$gender=$_GET['gender'];
$mac=$_GET['mac'];

db::exec("insert into fb_devices set fbid=$fbid,email='$email',gender='$gender',mac='$mac'");
die("1");
