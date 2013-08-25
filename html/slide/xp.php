<?php
require_once('/var/www/lib/functions.php');

$picName=$_GET['picId'];
$xp=$_GET['xp'];
$mac=$_GET['mac'];


db::exec("update appuser set xp=xp+$xp where mac='$mac' and app='slide'");
db::exec("update slide_assets set xpsolved=xpsolved+$xp where picname='$picName'");
db::exec("insert into slide_xp set mac='$mac',xp=$xp,created=now(),picname='$picName'");
echo "1";
