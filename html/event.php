<?php
require_once("/var/www/lib/functions.php");
$uid=intval($_GET['uid']);
echo "update appuser set xp = xp+3 where id=$uid";
db::exec("update appuser set xp = xp+3 where id=$uid");
