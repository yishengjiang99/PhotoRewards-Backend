<?php 
require_once("/var/www/lib/functions.php");
$rid=intval($_GET['id']);
if($rid!=0){
 db::exec("update PictureRequest set status=1 where id=$rid");
}

$rows=db::rows("select a.id, stars,title,cash_bid,gender,username,modified from PictureRequest a join appuser b on a.uid=b.id join fbusers f on b.fbid=f.fbid where b.stars>0 and a.title!='(null)' and b.fbid!=0 and 
a.created>date_sub(now(), interval 15 day) group by f.fbid order by modified");

echo rows2table($rows);
