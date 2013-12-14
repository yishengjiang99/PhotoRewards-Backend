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
 $country=$user['country'];
 if($url=="http://i.imgur.com/L2Qs1qh.png?t=1"){
  $url="http://ar.aarki.net/garden?src=32B95C7280DC09E1AA&advertising_id=$idfa&country=$country&user_id=$uid&exchange_rate=400";
  header("location: $url");
  exit;  
 }
if($url=="http://i.imgur.com/EApqc4n.png?t=1"){
  $url="http://www.supersonicads.com/delivery/mobilePanel.php?applicationUserId=$uid&applicationKey=2d5dc8b9&deviceOs=ios&deviceIds[IFA]=$idfa%20&currencyName=Points";
  error_log($url);
  header("location: $url");
  exit;
 }

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
