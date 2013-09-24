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
preg_match("/\((.*?) CPU (.*?) OS (.*?) like/", $ua,$m);
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
if($src=='appdog' && $registered=="0"){
  $subid=$_COOKIE['subid'];
  $user=db::row("select * from appuser where id=$uid");
  $mac=$user['mac'];
  $idfa=$user['idfa'];
  $h=md5($subid."supersssaaasla");
  $t=time();
  $ccb="http://stats.appdog.com/ads/cb.php?t=$t&h=$h&country=$countrystr&subid=$subid&idfa=$idfa&mac=$mac&ip=$ip";
  $jscb="<script src='$ccb' type='text/javascript'></script>";
  setcookie("registered", "1", time()+60*60*24*30*12);
}
$update="update appuser set deviceInfo='$dinfo',country='$countrystr', source='$src' where id=$uid limit 1";
error_log($update);
db::exec($update);
?>
<html>
<head>
<?php echo $jscb; ?>
<script type="text/javascript">
var fb_param = {};
fb_param.pixel_id = '6009503935704';
fb_param.value = '0.00';
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

</head>
<body>
<script>
window.location="<?php echo $cb."://"; ?>";
</script>
</body>
</html>
