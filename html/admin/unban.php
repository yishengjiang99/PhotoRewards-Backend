<?php
require_once("/var/www/lib/functions.php");
require_once("/var/www/lib/apns.php");
$uid=intval($_GET['uid']);
if(!$uid) die("no uid");
db::exec("update appuser set banned=0 where id=$uid");
apnsUser($uid,"your account was under review, but has been reactivated");
$user=db::row("select * from appuser where id=$uid");
var_dump($user);
