<?php
require_once("/var/www/lib/functions.php");
$go=urldecode($_GET['go']);
$subid=$_GET['subid'];
$st=explode(",",$subid);
$uid=intval($st[0]);
$offerID=$st[1];
$sql="insert into prclicks set subid='$subid', uid=$uid, offer_id='$offerID',created=now(), url='$go'";
db::exec($sql);
?>

<html>
<head>
<meta name="apple-mobile-web-app-capable" content="yes" />
</head>
<body>
<div style='height:1000px;display:block'>
</div>
<br><p>redirect</p>
<script>
if( !window.location.hash && window.addEventListener ){
    window.addEventListener( "load",function() {
        setTimeout(function(){
            window.scrollTo(0, 0);
	 window.location = '<?php echo $go ?>';

        }, 0);
    });
    window.addEventListener( "orientationchange",function() {
        setTimeout(function(){
            window.scrollTo(0, 0);
 window.location = '<?php echo $go ?>';

        }, 0);
    });
    window.addEventListener( "touchstart",function() {
         setTimeout(function(){
             window.scrollTo(0, 0);
 window.location = '<?php echo $go ?>';

         }, 0);
     });
}
window.top.scrollTo(0, 1);
 window.location = '<?php echo $go ?>';
</script>
</body></html>

