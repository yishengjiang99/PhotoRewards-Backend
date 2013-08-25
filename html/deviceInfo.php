<?php
$cb=$_GET['cb'];
$uid=intval($_GET['uid']);
require_once("/var/www/lib/functions.php");
$ua=$_SERVER['HTTP_USER_AGENT'];
preg_match("/\((.*?) CPU (.*?) OS (.*?) like/", $ua,$m);
$device=$m[1];
$os=$m[3];
$dinfo="$device|$os";

db::exec("update appuser set deviceInfo='$dinfo' where id=$uid");
//die("<h1>doing some db maintainance (loading yr backup files), will be back in half hour -- superadmin</h1>");
header("location: $cb://");
exit;
