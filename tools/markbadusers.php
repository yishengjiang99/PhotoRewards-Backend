<?php
require_once('/var/www/lib/functions.php');
$sql="select count(1) as cnt, uid from UploadPictures where type='DoneApp' and reviewed=-1 and created>date_sub(now(), interval 48 hour) group by uid having cnt>20";
$rows=db::rows($sql);
foreach($rows as $row){
 $unreviewed=db::rows("select * from UploadPictures where uid=".$row['uid']." and created>date_sub(now(), interval 2 hour)");
 foreach($unreviewed as $r){
    $pic=$r['id'];
    $update="update UploadPictures set reviewed=-1 where id='$pic' and reviewed=0";
echo "\n$update";
 }
 echo "\n".count($unreviewed);
}
