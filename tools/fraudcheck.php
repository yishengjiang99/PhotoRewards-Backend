<?php
require_once("/var/www/lib/functions.php");
$badips=db::rows("select ipaddress,count(1) as cnt from appuser where created>date_sub(now(),interval 1 hour) and ipaddress!='' group by ipaddress having cnt>5");
foreach($badips as $ip){
  $ipstr=$ip['ipaddress'];
  if($ipstr!=''){
    echo "update appuser set banned=1 where ipaddress='$ipstr'"; 
     $bu=db::rows("select id from appuser where ipaddrss='$ipstr' and banned=0");
     foreach($bu as $b){
	$bid=$b['id'];
	db::exec("update appuser set banned=1 where id=$bid");
	echo "\nupdate appuser set banned=1 where id=$bid";
      }
     db::exec("update appuser set banned=1 where ipaddress='$ipstr'");
  }
}
