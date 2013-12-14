<?php
require_once("/var/www/lib/functions.php");
$agent= 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';
$url="http://ar.aarki.net/offers?src=32B95C7280DC09E1AA&advertising_id=45A94FF1-EE7D-4F80-AC9D-EE655C9A3D75&country=US&user_id=85951&exchange_rate=400&user_ip=".getRealIP();
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_USERAGENT, $agent);
$result=curl_exec($ch);
var_dump($result);
var_dump(curl_getinfo($ch));
curl_close($ch);

