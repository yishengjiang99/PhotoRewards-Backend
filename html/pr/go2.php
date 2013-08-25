<?php
$pid=str_replace("http://www.json999.com/pr/uploads/","",$_GET['imgurl']);
$pid=str_replace(".jpeg?t=1","",$pid);
echo $pid;
$uid=$_GET['uid'];
header("location: /pr/p.php?uid=$uid&pid=$pid");
exit;
if(isset($_COOKIES['ids'])){
 $ids=$_COOKIES['ids'];
}
var_dump($_GET);
?>
<html>
 <a href='picrewards://' class=btn><h2>Back To PhotoRewards</h2></a>
<br><br>
<center>
<img src='<?= $_REQUEST['imgurl'] ?>'>
</center>

</html>
