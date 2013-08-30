<?php
require_once("/var/www/lib/functions.php");
$rr=db::rows("select id from appuser where app='picrewards' and tracking>0");
foreach($rr as $r){
 $uid=$r['id']; 
 $u="update appuser set tracking=0 where id=$uid";
 db::exec($u);
 echo $u;
}
