<?php
require_once("/var/www/lib/functions.php");
$uid=intval($_GET['uid']);
if(!$uid) die("no uid");
db::exec("update appuser set role=0 where id=$uid");

$user=db::row("select * from appuser where id=$uid");
var_dump($user);
