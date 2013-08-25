<?php
require_once("/var/www/lib/google.php");
$access=null;
$user;
if(isset($_COOKIE['google_access']) 
   && isset($_COOKIE['google_access_expires'])
   && intval($_COOKIE['google_access_expires']) > time() 
   && isset($_COOKIE['google_email'])){
	$access=$_COOKIE['google_access'];
	$user=getUser($_COOKIE['google_email']);
}elseif(isset($_COOKIE['google_email']) && isset($_COOKIE['google_refresh'])){
	$refresh=$_COOKIE['google_refresh'];
	$user=getUser($_COOKIE['google_email']);
	$token=refresh_token($refresh);	
	if(!$user || !isset($token['access_token'])){
		header("location: /goauth.php");
	}
	$access=$token['access_token'];
	updateTokenDb($user['id'],$token);		
	updateCookies($token,$user);
}else{
	header("location: ".ytUrl());
}

