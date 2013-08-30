<?php
require_once("/var/www/lib/apns.php");
$rr=file_get_contents("history.uids");
foreach(explode("\n",$rr) as $r){
if($r!="2902") continue;
echo "\n$r";

apnsUser($r,"I fixed the history tab... sorry about that!");
}
