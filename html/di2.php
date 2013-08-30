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

db::exec("update appuser set deviceInfo='$dinfo',country='$countrystr' where id=$uid");
//die("<h1>doing some db maintainance (loading yr backup files), will be back in half hour -- superadmin</h1>");

//header("location: $cb://");
//exit;
?>
<html>
<head>
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
