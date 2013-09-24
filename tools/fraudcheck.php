<?php
require_once("/var/www/lib/functions.php");
$badips=db::rows("select ipaddress,count(1) as cnt from appuser where created>date_sub(now(),interval 1 hour) and ipaddress!='' group by ipaddress having cnt>2");
foreach($badips as $ip){
  $ipstr=$ip['ipaddress'];
  if($ipstr!=''){
	echo "\nselect id from appuser where ipaddress='$ipstr' and banned=0";
     $bu=db::rows("select id from appuser where ipaddress='$ipstr' and banned=0  and created>date_sub(now(), interval 1 hour)");
     foreach($bu as $b){
	$bid=$b['id'];
	db::exec("update appuser set banned=1 where id=$bid");
//	echo "\nupdate appuser set banned=1 where id=$bid";
      }
  }
}
$badidfa=db::rows("select substring_index(idfa,'-',2) as idfa, count(1) as cnt from appuser where created>date_sub(now(),interval 3 hour) and idfa!='(null)' and idfa!='' and idfa!='00000000-0000-0000-0000-000000000000' 
group by substring_index(idfa,'-',2) having cnt>2");
foreach($badidfa as $idfa){
  $idfastr=$idfa['idfa']."%";
  if($idfastr!=''){
echo "\nselect id from appuser where idfa like '$idfastr' and banned=0";
     $bu=db::rows("select id from appuser where idfa like '$idfastr' and banned=0  and created>date_sub(now(), interval 3 hour)");
     foreach($bu as $b){
        $bid=$b['id'];
        db::exec("update appuser set banned=1 where id=$bid");
   //     echo "\nupdate appuser set banned=1 where id=$bid";
      }
  }
}

$badidfa=db::rows("select substring_index(mac,':',3) as mac, count(1) as cnt from appuser where created>date_sub(now(),interval 3 hour) and mac!='ios7device' group by substring_index(mac,':',3) having cnt>4");

foreach($badidfa as $idfa){
  $macstr=$idfa['mac']."%";
  if($macstr!=''){
echo "\nselect id from appuser where mac like '$macstr' and banned=0";
     $bu=db::rows("select id from appuser where mac like '$macstr' and banned=0 and created>date_sub(now(), interval 3 hour)");
     foreach($bu as $b){
        $bid=$b['id'];
        db::exec("update appuser set banned=1 where id=$bid");
        echo "\nupdate appuser set banned=1 where id=$bid";
      }
  }
}


