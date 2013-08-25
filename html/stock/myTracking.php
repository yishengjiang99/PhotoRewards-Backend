<?php
require_once("/var/www/lib/functions.php");
$mac=$_GET['mac'];
$user=db::row("select * from appuser where mac='$mac'");
$uid=$user['id'];

die(json_encode(array(
"tracking"=>db::rows("select * from stock_tracking where user_id=$uid"),
"iap"=>db::rows("select iap_id, date_format(max(expires),'%Y-%m-%d') as expires, max(extra) as extra from iap where user_id=$uid group by iap_id"),
)));
