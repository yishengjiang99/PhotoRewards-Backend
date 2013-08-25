<?php

require_once('/var/www/lib/functions.php');

$mac=$_GET['mac'];
$assetName=$_GET['assetName'];
db::exec("insert into unlocked_assets set mac='$mac', asset_name='$assetName', created=now()");
die("1");
