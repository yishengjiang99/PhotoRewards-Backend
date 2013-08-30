<?php
require_once("/var/www/tools/apns.php");
$uid=intval($_GET['uid']);
apnsUser($uid,"spinner","***SLOT MACHINE***\n[o] [ ] [ ]\n[-] [ ] [ ]\n[*] [ ] [ ]\nclick OK","http://www.json999.com/pr/spin.php?uid=$uid");
header("location: picrewards://");
exit;
/*
sleep(1);
apnsUser($uid,"spinner","***SLOT MACHINE***\n[o] [x] [ ]\n[-] [-] [ ]\n[*] [-] [ ]");
sleep(1);
apnsUser($uid,"spinner","***SLOT MACHINE***\n[o] [x] [*]\n[-] [-] [-]\n[*] [-] [*]");
sleep(1);
apnsUser($uid,"spinner","***SLOT MACHINE***\n[o] [x] [*]\n[-] [-] [-]\n[*] [-] [*]\n\nYOU WON!!");
*/
?>
