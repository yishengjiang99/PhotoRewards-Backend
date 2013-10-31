<?php
foreach(array("click","mobp","everb","appdog","aarki","supersonic","sponsorpay") as $n){
 echo "<br><a href='/admin/conversions.php?n=$n'>$n</a>";
}
date_default_timezone_set("UTC");
echo "<br>";
echo date("Y-m-d H:i:s");
if(isset($_GET['n'])) $n=" and network like '%".$_GET['n']."%' ";
else $n="";
require_once("/var/www/lib/functions.php");
if($_GET['n']=="aarki" || $_GET['n']=="supersonic" || $_GET['n']=="sponsorpay"){
 echo rows2table(db::rows("select a.id,a.ipAddress,a.country,a.created,b.Amount,b.transactionID,b.revenue,b.created, uploaded_picture,sub2 
  from sponsored_app_installs b join appuser a on b.uid=a.id where network like '".$_GET['n']."' order by b.created desc limit 1000"));
}else{
echo rows2table(db::rows("select a.uid,appid,c.deviceInfo,uploaded_picture,a.created,floor(a.Amount)/10 as userpay,a.revenue,a.network,b.name 
from sponsored_app_installs a join apps b on a.appid=b.id join appuser c on a.uid=c.id where network!='santa' $n order by a.created desc limit 100"));
}
