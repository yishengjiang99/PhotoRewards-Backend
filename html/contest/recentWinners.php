<?php
require_once("/var/www/lib/functions.php");
header('Content-Type: application/json');
$sql="select r.Img,a.created,r.name,r.CashValue, b.username,b.fbid from contest_winner a join appuser b on a.uid=b.id join rewards r on a.reward_id=r.id order by a.created desc";
$winners=db::rows($sql);
$ret=array();

foreach($winners as $winner){
 $ret[]=array('mainText'=>$winner['username']." won a ".$winner['name'],
 "details"=>$winner['created'],"imgUrl"=>$winner['Img']);
}
die(json_encode($ret));

