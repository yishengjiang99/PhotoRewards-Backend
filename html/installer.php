<?php
if(isset($_GET['mac'])){
 setcookie("mac",$_GET['mac'],time()+60*60*24*360,"/","json999.com");
}
$cb="hotdealz";
if(isset($_GET['cb'])){
$cb=$_GET['cb'];
}
?>
<html>
<head>
</head>
<body>
</body>
<script>
setTimeout(function(){window.location="<?=$cb ?>://";},1);
</script>
</html>
