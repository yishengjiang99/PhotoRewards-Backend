<html>
<body>
WELL COME TO HOT DEALS!!
<?php
$loc=null;
if(isset($_GET['loc'])){
 $loc=$_GET['loc'];
}
if(isset($_COOKIE['loc'])){
 $loc=$_COOKIE['loc'];
}

if($loc){
$loct=explode("|",$loc);
setcookie("loct",$loc,10000);
$lat = $loct[0];
$long=$loct[1];

require_once("key.php");

$url= "http://api.groupon.com/v2/deals.json?client_id=$key&lat=$lat&lng=$long&radius=20";
$deals=json_decode(file_get_contents($url),1);
$deals=$deals['deals'];
$html="<table border=1>";
foreach($deals as $deal){
	$options=$deal['options'];
	$option=$options[0];	
	$original=$option['value']['formattedAmount'];
	$now=$option['discount']['formattedAmount'];
	$off=$option['discountPercent'];
        $click_url=urlencode($deal['dealUrl']);
	$cj="http://www.anrdoezrs.net/click-$cjpid-$grouponAID?url=$click_url";	
	$html.="<tr><td>".$deal['announcementTitle']."</td><td><img src=".$deal['grid6ImageUrl']."></td><td><li>Original:<STRIKE>$original</STRIKE> <li>Now at: $now <li><b>$off % off</b></td><td><a href=$cj>GET IT NOW!!</td></tr>";
}

$html.="</table>";
echo $html;
}
?>


</body>
<script>
function showMap(position){
 window.location.href="index.php?loc="+position.coords.latitude+"|"+position.coords.longitude;
}
function handleError(error){
alert(error.toString());
}
<?php
if(!$loc){?>
 navigator.geolocation.getCurrentPosition(showMap,handleError);
<?php } ?>
</script>
</html>
