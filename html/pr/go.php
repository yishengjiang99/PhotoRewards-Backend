<?php
///pr/go.php?imgurl=http://img.directtrack.com/clashgroup/1398.gif?h=1&uid=4411
$url=$_GET['imgurl'];
$uid=$_GET['uid'];
$pid=str_replace("http://www.json999.com/pr/uploads/","",$_GET['imgurl']);
$pid=str_replace(".jpeg?t=1","",$pid);

if(isset($_GET['uid'])){
 require_once("/var/www/lib/functions.php");
 $uid=$_GET['uid'];
 $user=db::row("select * from appuser where id=$uid");
 $idfa=$user['idfa'];
 $h2=md5($uid.$idfa."ddfassffseesfg");

 header("location: /pr/p22.php?uid=$uid&pid=$pid&h=".$h2);
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
