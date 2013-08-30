<?php
$today=date("Ymd");
$url="http://reportapiv2.mobpartner.mobi/report2.php?key=f34ecd396acede3d83ee72be8b3620d6&login=grepawk&period=today&subid=ALL&export=csv";
echo $url;
$tt=file_get_contents($url);
echo $tt;
$tt=explode("\n",$tt);
echo $tt;
