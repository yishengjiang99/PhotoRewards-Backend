<?php
$url=urldecode($_GET['url']);
$back=urldecode($_GET['cb']);
?>
<html>
<head>
<meta name = "viewport" content = "user-scalable=no, initial-scale=1.0, maximum-scale=1.0, width=device-width">
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
</head>
<body>
<a href='<?=$back ?>'><img height=30 src='http://www.json999.com/img/backbtn.jng'></a>
<br>
<iframe width=100% src='<?= $url ?>' height=1000px></iframe>
</body>
</htrml>
