<?php
require_once("/var/www/lib/functions.php");
$badips=db::rows("select substring_index(ipaddress,'.',3) as ipaddress, banned, count(1) as cnt from appuser where app='picrewards' and created>date_sub(now(),interval 5 hour) and ipaddress!='' 
group by substring_index(ipaddress,'.',3) having cnt>4");

$badips=array_merge($badips,
db::rows("select substring_index(ipaddress,'.',3) as ipaddress, banned, count(1) as cnt from appuser where app='picrewards' and created>date_sub(now(),interval 2 day) and ipaddress!='' 
 group by substring_index(ipaddress,'.',3) having cnt>7"));
foreach($badips as $ip){
  $ipstr=$ip['ipaddress'].".";
  $cnt=$ip['cnt'];$note="$cnt same ip $ipstr";
  db::exec("insert ignore into bannedIps set ip='$ipstr'");
  if($ipstr!=''){
     $bu=db::rows("select id from appuser where ipaddress like '$ipstr%' and banned=0 and created>date_sub(now(), interval 1 day) and app='picrewards'");
     foreach($bu as $b){
	$bid=$b['id'];
	if($bid==2902) continue; //superadmin
	db::exec("update appuser set banned=1, note='$note' where id=$bid");
	echo "\nupdate appuser set banned=1 where id=$bid";
      }
  }
}

$badidfa=db::rows("select substring_index(idfa,'-',2) as idfa, count(1) as cnt from appuser where created>date_sub(now(),interval 3 hour) and app='picrewards' and idfa!='(null)' and idfa!='' 
and idfa!='00000000-0000-0000-0000-000000000000' and idfa!='notios6yet' and idfa!='(null)'  
group by substring_index(idfa,'-',2) having cnt>5");

foreach($badidfa as $idfa){
   $idfastr=$idfa['idfa']."%";
    $cnt=$idfa['cnt'];$note="$cnt same idfa $idfastr";

  if($idfastr!=''){
     $bu=db::rows("select id from appuser where idfa like '$idfastr' and banned=0  and created>date_sub(now(), interval 3 hour)");
     foreach($bu as $b){
        $bid=$b['id'];
       if($bid==2902) continue; //superadmin

        db::exec("update appuser set banned=1,note='$note' where id=$bid");
   //     echo "\nupdate appuser set banned=1 where id=$bid";
      }
  }
}
$badidfa=db::rows("select substring_index(mac,':',4) as mac, count(1) as cnt from appuser where created>date_sub(now(),interval 3 hour) and mac!='ios7device' and app='picrewards' group by substring_index(mac,':',4) having cnt>3");

foreach($badidfa as $idfa){
  $macstr=$idfa['mac']."%";
  if($macstr!=''){
     $bu=db::rows("select id from appuser where mac like '$macstr' and banned=0 and created>date_sub(now(), interval 3 hour)");
     foreach($bu as $b){
        $bid=$b['id'];
	if($bid==2902) continue; //superadmin
	$note = "dup mac $mac";
        db::exec("update appuser set banned=1,note='$note' where id=$bid");
        echo "\nupdate appuser set banned=1 where id=$bid";
      }
  }
}


