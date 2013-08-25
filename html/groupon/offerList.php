<?
require_once("key.php");

$url= "http://api.groupon.com/v2/deals.json?client_id=$key";
$offers=json_decode(file_get_contents($url),1);
$deals=$offers['deals'];

$ret=array();
$deals = array_slice($deals,0,10);

foreach($deals as $deal){
        $options=$deal['options'];
        $option=$options[0];
        $original=$option['value']['formattedAmount'];
        $now=$option['discount']['formattedAmount'];
        $off=$option['discountPercent'];
        $click_url=urlencode($deal['dealUrl']);
        $cj="http://www.anrdoezrs.net/click-$cjpid-$grouponAID?url=$click_url";
	$ret[]=array("name"=>$deal['announcementTitle'],"imgUrl"=>$deal['smallImageUrl'],"original"=>$original,"discount"=>$off."% off","cost"=>$now,"url"=>$cj);
}
die(json_encode($ret));

