<?php
$cb=$_GET['cb'];
$uid=intval($_GET['uid']);
require_once("/var/www/lib/functions.php");
 $ip=getRealIP();
 $iplong=ip2long($ip);
 $countrystr='US';
 $country=db::row("select * from ipcountry where $iplong>ipFROM and $iplong<ipTO");
 if($country){
   $countrystr=$country['countrySHORT'];
 }
$ua=$_SERVER['HTTP_USER_AGENT'];
preg_match("/\((.*?)CPU(.*?) OS (.*?) like/", $ua,$m);
$device=$m[1];
$os=$m[3];
$dinfo="$device|$os";
$src="";
if(isset($_COOKIE['src'])){
        $src=$_COOKIE['src'];
}
$registered="0";
if(isset($_COOKIE['registered'])){
        $registered=$_COOKIE['registered'];
}
$jscb="";

$inviter=0;
$user=db::row("select * from appuser where id=$uid");
$idfa=$user['idfa'];
$mac=$user['mac'];

if(isset($_COOKIE['inviter']) && intval($_COOKIE['inviter'])!=0 && $user['inviter_id']==0){
error_log("inviter ".$_COOKIE['inviter']." in the cookie");
    $inviter=$_COOKIE['inviter'];
}

if($src=='appdog' && $registered=="0"){
  $subid=$_COOKIE['subid'];
  $mac=$user['mac'];
  $idfa=$user['idfa'];
  $h=md5($subid."supersssaaasla");
  $t=time();
  $ccb="http://stats.appdog.com/ads/cb.php?t=$t&h=$h&country=$countrystr&subid=$subid&idfa=$idfa&mac=$mac&ip=$ip";
  $jscb="<script src='$ccb' type='text/javascript'></script>";
  setcookie("registered", "1", time()+60*60*24*30*12);
}
if(isset($_COOKIE['idfa']) && $_COOKIE['idfa']!=$idfa){
 $rc=intval($_COOKIE['rc'])+1;
 setcookie("rc",$rc,time()+60*60*24*7);
 $multi=$_COOKIE['multi'];
 $multi.=$idfa;
 setcookie("multi",$multi,time()+60*60*24*2);
 error_log("setting rc to $rc".json_encode($_COOKIE)." idfa ".$idfa." history $multi");
 if($rc>7){
	db::exec("update appuser set banned=1, note='different cookie value for idfa:$multi' where id=$uid");
 }
}
setcookie("idfa",$idfa,time()+60*60*24*30*12);
setcookie("mac",$mac,time()+60*60*24*30*12);
$update="update appuser set deviceInfo='$dinfo',country='$countrystr', source='$src',inviter_id=$inviter where id=$uid limit 1";
error_log($update);
db::exec($update);
$sessionId=$uid;
if($inviterId!=0){
 $t=time();
 $h=md5($t.$idfa."what1sdns?");
 $inviter=db::row("select username from appuser where id=$inviterId");
 $code=$inviter['username'];
 $url="https://json999.com/pr/bonus.php?interval=1&uid=$uid&idfa=$idfa&t=$t&h=$h&code=$code";
 error_log($url);
 file_get_contents($url);
}
?>
<html>
<head>
<?php echo $jscb; ?>
<script type="text/javascript">
var fb_param = {};
fb_param.pixel_id = '6009503935704';
fb_param.value = '0.01';
fb_param.currency = 'USD';
(function(){
  var fpw = document.createElement('script');
  fpw.async = true;
  fpw.src = '//connect.facebook.net/en_US/fp.js';
  var ref = document.getElementsByTagName('script')[0];
  ref.parentNode.insertBefore(fpw, ref);
})();
</script>
<noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/offsite_event.php?id=6009503935704&amp;value=0&amp;currency=USD" /></noscript>
<img height="1" width="1" alt="" src="http://panel.gwallet.com/network-node/track/19e7b771c1a4b0c5/1x1.gif">
</head>
<body>
<script>
window.location="<?php echo $cb."://"; ?>";
</script>
</body>
</html>
