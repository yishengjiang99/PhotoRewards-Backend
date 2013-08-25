<?php
require_once("/var/www/lib/functions.php");
$rows=db::rows("select storeID from offers where storeID !=0");
foreach($rows as $r){
	$url='http://json999.com/appmeta.php?id='.$r['storeID'];
 file_get_contents($url);
}
