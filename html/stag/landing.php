<?php
$id=intval($_GET['oid']);
require_once("/var/www/lib/functions.php");
$row=db::row("select * from offers where id=$id");
$moburl=$row['click_url'];
 if (ereg('iPhone',$_SERVER['HTTP_USER_AGENT'])) {
	header("location: $moburl");
    exit;
 }else if(ereg('Android',$_SERVER['HTTP_USER_AGENT'])){
    header("location: $moburl");
    exit;
 }
?>

<html>
<body>
<br><br><br>
<br><br><br>

<center>
<h1>Call Now: 888-657-3708</h1>
<a href="<?=$moburl?>" target="_top"><img src="http://c.5ribs.com/advertiser_creatives/18917_original.png" alt="Call Now: 888-657-3708" border="0"/></a>
</center>
</body>
</html>
