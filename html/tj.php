<?php
$idfa=$_GET['idfa'];

$appid="144e2cd6-9009-4120-9623-7b9aa225a14d";
$userid=$_GET['uid'];
$udid="edbbf1dcbd52707467d091b83e5fda26c718be24";
$url="http://ws.tapjoyads.com/get_offers?type=1&json=1&app_id=$appid&publisher_user_id=$userid&device_type=iPhone&country_code=US&language=EN&max=35&os_version=6.1.3&device_ip=96.247.52.15&start=0&advertiser_id=$idfa&udid=$udid";
echo $url;
echo file_get_contents($url);
