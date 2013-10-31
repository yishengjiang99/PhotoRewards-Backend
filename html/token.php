<?php
require_once('/var/www/lib/functions.php');
$app=stripslashes($_GET['app']);
if(!$app) $app='slide';
$idfa=stripslashes($_GET['idfa']);
$mac=stripslashes($_GET['mac']);
$token=stripslashes($_GET['token']);
$uid=0;
if(isset($_GET['uid'])) $uid=intval($_GET['uid']);
db::exec("insert ignore into pushtokens set mac_address='$mac', app='$app',token='$token',idfa='$idfa', uid='$uid', created=now() on duplicate key update idfa='$idfa',uid=$uid");
echo "1";
exit;
