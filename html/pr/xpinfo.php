<?php
require_once("/var/www/lib/functions.php");
require_once("/var/www/html/pr/levels.php");

$mac=$_GET['mac'];
$idfa=$_GET['idfa'];
$cb=$_GET['cb'];
$user=db::row("select * from appuser where app='$cb' and mac='$mac'");
$uid=$user['uid'];
$username=$user['username'];
$xpinfo=getBonusPoints($user['xp']);
$maxbonus=$xpinfo['maxbonus'];
$ret=$xpinfo;

$longmsg="Download #PhotoRewards from the AppStore and enter my bonus code '$username' for up to $maxbonus! Bonus Points.\nPoints can be redeemed for Amazon.com, Starbucks, iTunes, Hulu Plus giftcards or PayPal Cash!";
$longmsg.=" http://www.json999.com/redirect.php?uid=$uid";
$ret['tweetmsg']="Download #PhotoRewards from the AppStore and enter my bonus code '$username' for up to $maxbonus!";
$ret['emailmsg']=$longmsg;
$ret['fbmsg']=$longmsg;
$ret['redirect']="http://www.json999.com/redirect.php?uid=$uid";
die(json_encode($ret));
