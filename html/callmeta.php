<?php
require_once("/var/www/lib/functions.php");
$rows=db::rows("select storeID from offers where storeID !=0");
foreach($rows as $r){
	$url='http://json999.com/ap2.php?appid='.$r['storeID'];
echo $url;
 file_get_contents($url);
}
