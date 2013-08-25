<?
require_once("/var/www/lib/db.class.php");
$ip=$_SERVER['REMOTE_ADDR'];
$tid=intval($_GET['tid']);
$val=addslashes($_GET['value']);
db::exec("insert ignore into thread_category set tid=$tid, category='$val', ip='$ip'");

die($_GET['value']);
