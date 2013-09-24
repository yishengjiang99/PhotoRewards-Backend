<?php
require_once('/var/www/lib/functions.php');
$app=stripslashes($_GET['app']);
if(!$app) $app='slide';
$idfa=stripslashes($_GET['idfa']);
$mac=stripslashes($_GET['mac']);
$token=stripslashes($_GET['token']);
$uid=0;
if(isset($_GET['uid'])) $uid=intval($_GET['uid']);
///token.php?app=picrewards&mac=ios7device&token=86fbb74163669c507da9d556656677fdedf019c92af1c84b4f701bf373ae6c70&idfa=DE99B609-4618-4D72-BA9D-342568ABC8C5&mac=ios7device&cb=picrewards&idfa=DE99B609-4618-4D72-BA9D-342568ABC8C5&t=1379642227&h=0dd10ed49ba506506a3955d3f1fc9861
db::exec("insert ignore into pushtokens set mac_address='$mac', app='$app',token='$token',idfa='$idfa', created=now() on duplicate key update idfa='$idfa'");
echo "1";
exit;
