<?php
require_once("/var/www/lib/functions.php");
require_once("/var/www/lib/apns.php");
$uid=intval($_GET['uid']);
if(!$uid) die("no uid");
db::exec("update appuser set banned=0,role=2 where id=$uid");
//apnsUser($uid,"Your bonus code has been promoted to: special, accessible to brand new users");
$user=db::row("select * from appuser where id=$uid");
var_dump($user);
