<?php 
require_once("/var/www/lib/functions.php");

$last=intval($_POST['last']);
if($last){
 $pics=db::rows("select a.id, a.reviewed, a.created as uploaded,b.created as approved from 
UploadPictures a join PictureMonitor b on a.id=b.pid where a.reviewed!=0 and b.created>date_sub(now(), interval $last minute)");
 $confirm=rows2table($pics);
 foreach($pics as $p){
   $pid=$p['id'];
   db::exec("update UploadPictures set reviewed=0 where id='$pid'");
 }
}else{
  $recent=db::rows("select a.id, a.reviewed as approved, a.created as uploaded, b.created as reviewed from UploadPictures a join PictureMonitor b on a.id=b.pid where a.reviewed!=0 order by reviewed desc");
}
echo "<a href=/admin/safety.php>back</a>";
echo "<br><form method=POST>";
echo "Revert pictures approved in the last: <select name=last>";
foreach(array(5,15,30,60,120,240) as $lm){
 echo "<option value=$lm>$lm minute</option>";
}
echo "<br><input type=submit /></form>";
if($confirm){
 echo "pictures reverted";
  echo $confirm;
}
if($recent){
 echo "Recently reviewed";
 echo rows2table($recent);
}
?>

