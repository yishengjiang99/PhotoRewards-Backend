<?
require_once("/var/www/lib/functions.php");
$_GET=$_REQUEST;
$msgid=intval($_GET['msgid']);
if($msgid<3) die();
db::exec("update inbox set readmsg=1 where id=$msgid");
