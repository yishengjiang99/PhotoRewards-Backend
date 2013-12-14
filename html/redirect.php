<?php
if($_GET['dest']=='contest'){
   header("location: https://itunes.apple.com/us/app/photo-contest-share-photos/id755182884?ls=1&mt=8");
  exit;
}
if($_GET['from']=='tapsense'){
  $ipAddress=getRealIP();
   $ua=$_SERVER['HTTP_USER_AGENT'];
  db::exec("insert into incoming_clicks set subid='$ipAddress', created=now(),deviceInfo='$ua',ip='$ipAddress',source='tapsense'");
  setcookie("src", $_GET['from'],time()+60*60*24*30);
  header("location: https://itunes.apple.com/app/photorewards/id662632957?mt=8");
  exit;
}
if(isset($_GET['src'])){
 $src=$_GET['src'];
 if($src=='appdog'){
    setcookie("src", $src,time()+60*60*24*30);
    setcookie("subid", $_GET['subid'],time()+60*60*24*30);
    setcookie("registered", "0", time()+60*60*24*30*12);
    require_once('/var/www/lib/functions.php');
    $ua=$_SERVER['HTTP_USER_AGENT'];
    $sid=$_GET['subid'];
    $ipaddress=getRealIP();
    db::exec("insert into incoming_clicks set subid='$sid', created=now(),deviceInfo='$ua',ip='$ipaddress',source='appdog'");
 }
 header("location: https://itunes.apple.com/app/photorewards/id662632957?mt=8");
 exit;
}

if(isset($_GET['nn']) && $_GET['nn']!=''){
 require_once("/var/www/lib/functions.php");
 $username=stripslashes($_GET['nn']);
 $sql="select * from appuser where username='$username'";
 $inviter =db::row($sql);
 $inviterId=$inviter['id'];
 setcookie("inviter", $inviterId,time()+60*60*24*30,"/","json999.com");
 $ua=$_SERVER['HTTP_USER_AGENT'];
 $ipaddress=getRealIP();
 db::exec("insert into incoming_clicks set subid='$inviterId', created=now(),deviceInfo='$ua',ip='$ipaddress',source='bonuscode'");
 header("location: https://itunes.apple.com/app/photorewards/id662632957?mt=8");
 exit;
}

if(isset($_GET['uid']) && !isset($_COOKIE['inviter'])){
 setcookie("inviter", $_GET['uid'],time()+60*60*24*30,"/","json999.com");
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
 $e='fb_invited';
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
exit;
?>
