<?php 
$ch = curl_init();
$j=$_GET['cookiejar'];
curl_setopt($ch, CURLOPT_COOKIEJAR, "/var/www/html/cookies/cookiejar$j");
curl_setopt($ch, CURLOPT_URL,"http://www.xoxohth.com/login.php");
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_HEADER, 1);
curl_exec($ch);

curl_setopt($ch,CURLOPT_URL,"http://www.xoxohth.com/securimage/securimage_show.php");
curl_setopt($ch,CURLOPT_REFERER,"http://www.xoxohth.com/");
curl_setopt($ch, CURLOPT_HEADER, false);
$img=curl_exec($ch);
  header('Content-Type: image/png');
header("Content-Length: " . filesize($img));
    echo($img);
exit;
