<?php
foreach(array("click","mobp","everb","appdog","virool") as $n){
 echo "<br><a href='/admin/conversions.php?n=$n'>$n</a>";
}
date_default_timezone_set("UTC");
echo date("Y-m-d H:i:s");
if(isset($_GET['n'])) $n=" and network like '%".$_GET['n']."%' ";
else $n="";
require_once("/var/www/lib/functions.php");
echo rows2table(db::rows("select a.uid,appid,c.deviceInfo,uploaded_picture,a.created,floor(a.Amount)/10 as userpay,a.revenue,a.network,b.name 
from sponsored_app_installs a join apps b on a.appid=b.id join appuser c on a.uid=c.id where network!='santa' $n order by a.	created desc limit 100"));
