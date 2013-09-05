<?php
require_once("/var/www/html/pr/apns.php");
$aaa=explode("\n",trim(file_get_contents("cc")));
 foreach($aaa as $a){
   apnsUser($a,"We sent you an invalid $5 code.. sorry! Points have been credited by to your account");
}
