<?php
require_once('/var/www/lib/functions.php');
$empty=db::rows("select * from appuser where ipaddress!='' and country=''");

foreach($empty as $e){
 $ip=$e['ipaddress'];
 $iplong=ip2long($ip);
// echo "\n$iplong";continue;

 $country=db::row("select * from ipcountry where $iplong>ipFROM and $iplong<ipTO");
 if($country){
  
   $country=$country['countrySHORT'];
  $update="update appuser set country='$country' where id=".$e['id'];
db::exec($update);
  echo "\n$update";
 }
}
