<?php
require_once("/var/www/lib/google.php");
require_once("/var/www/lib/db.class.php");


$code=$_GET['code'];
$scope=$_GET['state'];
$token=create_access_token($code);
if($access=$token['access_token']){
        $user=guser($access);
        $uid=createUser($user);
	updateCookies($token,$user);
	updateTokenDb($uid,$token,$scope);
}
header("location: /home.php");
