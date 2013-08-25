<?
require_once("/var/www/lib/db.class.php");
$ip=$_SERVER['REMOTE_ADDR'];
$tid=intval($_GET['tid']);
db::exec("insert into thread_ratings set tid=$tid, likethread=1,ip='$ip'");
die("180!!  ");
