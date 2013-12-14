<?php
require_once("/var/www/lib/functions.php");

$rname=$_GET['name'];

if($app=db::row("select * from apps where Name like '".htmlspecialchars_decode($rname)."%'")){
 die(json_encode($app));
}

$url="https://itunes.apple.com/search?term=".$rname."&entity=software";
$appjson=json_decode(file_get_contents($url),1);
if($appjson['results'] && $appjson['results'][0]){
 $apparr=$appjson['results'][0];
 $name=$apparr['trackName'];
 $appid=$apparr['trackId'];
 $imgurl=$apparr['artworkUrl60'];
 $screenshots=implode(",",$apparr['screenshotUrls']);
 $url=$apparr['trackViewUrl'];
 $name=addslashes($name);
 db::exec("insert ignore into apps set id=$appid, Name='$name',IconURL='$imgurl',RedirectURL='$url',screenshot='$screenshots'");
 error_log("insert ignore into apps set id=$appid, Name='$name',IconURL='$imgurl',RedirectURL='$url',screenshot='$screenshots'");
 error_log("select * from apps where Name like '".urldecode($rname)."%'");
 if($app=db::row("select * from apps where Name like '".urldecode($rname)."%'")){
 	die(json_encode($app));
 }
}

