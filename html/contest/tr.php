<?php
require_once("/var/www/lib/functions.php");
$idfa=$_GET['idfa'];
$offers="select a.id as contestId, TIMESTAMPDIFF(HOUR, now(), contest_ends) as hoursleft, r.name as reward, r.Img as rewardUrl from contest a join rewards r on a.reward_id=r.id where a.status=1 order by RAND()";
$offers=db::rows($offers);
$ret=array();
foreach($offers as $o){
if($o['hoursleft']<0) continue;
 $o['name']="Contest ends in ".$o['hoursleft']." hours!!";
 $o['reward']="Win a ".$o['reward'];
 $o['contestId']="la_".$o['contestId']."_spu";
 $o['redirectUrl']=str_replace("SUBID_HERE",$subid,$offer['redirectUrl']);
 $ret[]=$o;
}

$installedPR=db::row("select count(1) as cnt from appuser where idfa='$idfa' and app='picrewards'");
if($installedPr['cnt']=="0"){
 $o=array();
 $o['name']="Download for Free";
 $o['reward']="PhotoRewards: Share pitures for PayPal Cash and Gift Cards";
 $o['rewardUrl']="http://d1y3yrjny3p2xa.cloudfront.net/prbanner.jpg";
 $o['contestId']="crosspr";
 $o['redirectUrl']="";
 $ret[]=$o;
}
else{
error_log(" installed $idfa");
}
die(json_encode($ret));
