<?php
require_once("/var/www/lib/functions.php");

$appid=intval($_GET['appid']);
if(!$appid){
 die("[]");
}

if($app=db::row("select * from apps where id=$appid")){
 die(json_encode($app));
}

$url="http://itunes.apple.com/lookup?id=$appid";
$appjson=json_decode(file_get_contents($url),1);
if($appjson['results'] && $appjson['results'][0]){
 $apparr=$appjson['results'][0];
 $name=$apparr['trackName'];
 $imgurl=$apparr['artworkUrl60'];
 $screenshots=implode(",",$apparr['screenshotUrls']);
 $url=$apparr['trackViewUrl'];
$name=addslashes($name);
 db::exec("insert ignore into apps set id=$appid, Name='$name',IconURL='$imgurl',RedirectURL='$url',screenshot='$screenshots'");
 if($app=db::row("select * from apps where id=$appid")){
 	die(json_encode($app));
  }
}

