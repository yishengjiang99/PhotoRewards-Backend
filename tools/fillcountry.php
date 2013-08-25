<?php
require_once('/var/www/lib/functions.php');
$empty=db::rows("select * from appuser where ipaddress!='' and country=''");
foreach($empty as $e){
 $ip=$e['ipaddress'];
 $ipt=substr($ip,0,strrpos($ip,'.'));


 $country=db::row("select * from ipcountry where ip like '$ipt%'");
 if($country){
   $country=$country['country'];
  $update="update appuser set country='$country' where id=".$e['id'];
  echo "\n$update";
 }
}
