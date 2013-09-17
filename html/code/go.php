<?php
///pr/go.php?imgurl=http://img.directtrack.com/clashgroup/1398.gif?h=1&uid=4411
$url=$_GET['imgurl'];
$uid=$_GET['uid'];
if(strpos($url,"1306")!==false){
 $go="http://t.mobitrk.com/?a=t&aff_id=502&tags=2901,71&o_id=1306";
//http://ads.impact-mobi.com/ez/bsykfvsdd/&subid1=1399_$uid";
 header("location: $go");
exit;
}



$pid=str_replace("http://www.json999.com/pr/uploads/","",$_GET['imgurl']);

$pid=str_replace(".jpeg?t=1","",$pid);

if(isset($_GET['uid'])){
 $uid=$_GET['uid'];
 header("location: /pr/p.php?uid=$uid&pid=$pid");
 exit;
}
?>
<html>
 <a href='picrewards://' class=btn><h2>Back To PhotoRewards</h2></a>
<br><br>
<center>
<img src='<?= $_REQUEST['imgurl'] ?>'>
</center>

</html>
