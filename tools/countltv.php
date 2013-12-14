<?php 
exit;
require_once("/var/www/lib/functions.php");
$rows=db::rows("select uid, sum(revenue) as ltv from sponsored_app_installs group by uid");

foreach($rows as $r){
 $uid=$r['uid'];
 $ltv=$r['ltv'];
 $update="update appuser set ltv=$ltv where id=$uid";
echo "\n$update";
db::exec($update);
}
