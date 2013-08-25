<?php
if(strpos($_GET['from'],'invideDone')!==false){
 require_once('/var/www/lib/functions.php');
 $rid=$_GET['request'];
 $to=$_GET['to'];
 if(!$to || count($to)==0){
   header("location: picrewards://");
   exit;
 }
 $uid=intval(str_replace("invideDone","",$_GET['from']));
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
 header("location: picrewards://");
 require_one("/var/www/html/pr/apns.php");
 apnsUser($uid,"","You win!$xp XP earned for inviting $cnt friends");
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
