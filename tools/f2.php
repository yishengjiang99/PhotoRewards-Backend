<?php
require_once("/var/www/lib/functions.php");

$badidfa=db::rows("select substring_index(mac,':',4) as mac, count(1) as cnt,email,avg(ltv),avg(stars) from appuser where mac!='ios6device' and mac!='ios6device' group by substring_index(mac,':',4) having cnt>1 and cnt<11;");
foreach($badidfa as $idfa){
  $macstr=$idfa['mac']."%";
  if($idfastr!=''){
echo "\nselect id from appuser where mac like '$macstr' and banned=1";
continue;
     $bu=db::rows("select id from appuser where mac like '$macstr' and banned=0");
     foreach($bu as $b){
        $bid=$b['id'];
        db::exec("update appuser set banned=1 where id=$bid");
 //       echo "\nupdate appuser set banned=1 where id=$bid";
      }
  }
}


