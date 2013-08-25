<?php
$r=$_REQUEST;

$imei=$_REQUEST['imei'];
$androidId=$_REQUEST['androidId'];
$serial=$r['serial'];
setcookie("serial",$serial,time()+60*60*24*30*12);
setcookie("imei",$imei,time()+60*60*24*30*12);
setcookie("androidId",$androidId,time()+60*60*24*30*12);
setcookie("uid",$uid,time()+60*60*24*30*12);
header("location: m.php");
