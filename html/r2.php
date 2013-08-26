<?php
require_once("/var/www/lib/functions.php");
$uid=$_GET['uid'];
$rows=db::rows("select * from rewards order by display_order asc");
foreach($rows as &$r){
 if($r['requiresEmail']=="0") $r['postext']=$r['postext']."\n\rGift Card code will be available instantly and recorded under 'My Account' -> 'History'";
// unset($r['Img']);
 $r['requiresEmail']=0;
}
die(json_encode($rows));
