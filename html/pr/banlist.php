<?php
/*
166.137.191.39 - - [18/Sep/2013:02:55:42 +0000] "GET /pr/banlist.php?ref=5767&h=9df2b539c354ed032fc12414f02da7da HTTP/1.1" 404 293 "-" "Mozilla/5.0 (iPhone; CPU iPhone OS 6_1_3 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10B329 Safari/8536.25"
*/

$rid=intval($_GET['ref']);
$h=md5($rid."adsbdds");
if($_GET['h']!=$h) die("die");

require_once("/var/www/lib/functions.php");
$r=db::row("select * from PictureRequest where id=$rid");
$dbrid=$r['id'];
$uid=$r['uid'];
$title=$r['title'];
db::exec("update PictureRequest set status=-2 where id=$dbrid and status!=3 limit 1");
db::exec("update appuser set banned=5 where id=$uid");
?>
<html>
<body>
<script>
alert("User has been banned!");
window.location="picrewards://";
</script>
</body>
</html>
