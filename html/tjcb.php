<?php
require_once("/var/www/lib/functions.php");
if($_GET['pw']!="1f1npTNDUDAdikNFEYqv") die(0);
$appid=intval($_GET['appid']);
$uid=intval($_GET['uid']);
$points=intval($_GET['amount'])*10;
//db::exec("update appuser set stars=stars+".$points." where id=$uid");

db::exec("insert ignore into sponsored_app_installs set uid=$uid, Amount=$points, appid=$appid, created=now()");
file_get_contents("http://json999.com/appmeta.php?appid=$appid");
echo 1;
