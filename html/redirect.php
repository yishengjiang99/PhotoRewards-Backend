<?php
if(isset($_GET['src'])){
 $src=$_GET['src'];
 if($src=='appdog'){
    setcookie("src", $src,time()+60*60*24*30);
    setcookie("subid", $_GET['subid'],time()+60*60*24*30);
    setcookie("registered", "0", time()+60*60*24*30*12);
     require_once('/var/www/lib/functions.php');
    $ua=$_SERVER['HTTP_USER_AGENT'];
    preg_match("/\((.*?) CPU (.*?) OS (.*?) like/", $ua,$m);
    $device=$m[1];
    $os=$m[3];
    $dinfo="$device|$os";
    $sid=$_GET['subid'];
    $ipaddress=getRealIP();
    db::exec("insert into incoming_clicks set subid='$sid', created=now(),deviceInfo='$dinfo',ip='$ipaddress'");
 }
 
 header("location: https://itunes.apple.com/app/photorewards/id662632957?mt=8");
 exit;
}
if(strpos($_GET['from'],'invideDone')!==false){
 require_once('/var/www/lib/functions.php');
 $rid=$_GET['request'];
 $to=$_GET['to'];
 if(!$to || count($to)==0){
   header("location: picrewards://");
   exit;
 }
 $uid=intval(str_replace("invideDone","",$_GET['from']));
 $avgCnt=db::row("select avg(count) as freq,count(1) as total from fbinvites where uid=$uid");
 if($avgCnt['freq']>2 && $avgCnt['total']>5){
   header("location: picrewards://");
   exit;
 }
 $cnt=count($to); $xp=5*$cnt;
 foreach($to as $i=>$toid){
   $friendfbid=$toid; 
   $sli="insert ignore into fbinvites set uid=$uid, to_fbid=$friendfbid,created=now(),request_id='$rid' on duplicate key update count=count+1, request_id=$rid";
   db::exec($sli);
 }
 $update="update appuser set xp=xp+$xp where id=$uid";
 $e='fb invited '.$cnt.' friends';
 db::exec("insert into pr_xp set uid=$uid,xp=$xp,created=now(),event='$e',mac='$rid'");
 db::exec($update);
 require_once("/var/www/html/pr/apns.php");
 apnsUser($uid,"You win! $xp XP earned for inviting $cnt friends.","You win! $xp XP earned for inviting $cnt friends.");
 header("location: picrewards://");
 exit;
}

 if (ereg('iPhone',$_SERVER['HTTP_USER_AGENT'])) {
   if(isset($_GET['surl'])){
	$GO=$_GET['surl'];
	header("location: $GO");
       exit;      
   }
    header("location: https://itunes.apple.com/app/id662632957?mt=8");
    exit;
 }else if(ereg('Android',$_SERVER['HTTP_USER_AGENT'])){
    header("location: https://play.google.com/store/apps/details?id=com.ragnus.grepawk");
   exit;
 }
 header("location: https://itunes.apple.com/app/id662632957?mt=8");
?>
