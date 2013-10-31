<?php
require_once("/var/www/lib/functions.php");
$uid=$_GET['uid'];
$rows=db::rows("select * from rewards where available>0 order by display_order asc");
foreach($rows as &$r){
 if($r['id']==1) $r['Description']="Be back on Monday"; 
 if($r['requiresEmail']=="0") $r['postext']=$r['postext']."\n\rGift Card code will be available instantly and recorded under 'My Account' -> 'History'";
}
die(json_encode($rows));
