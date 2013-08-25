<?
require_once("/var/www/lib/functions.php");


$fbid=$_GET['fbid'];
$pp=db::rows("select p.* from unlocked_assets p join fb_devices a on p.mac=a.mac where a.fbid=$fbid");
die(json_encode($pp));
