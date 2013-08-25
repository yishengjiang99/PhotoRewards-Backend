<?php
require_once("/var/www/lib/functions.php");
require_once("/var/www/lib/firewall.php");

$uid=intval($_GET['uid']);
$mid=md5($idfa);
$t=time();
$h=md5($uid.$mid."watup".$t);
$link="$uid&r=r&mid=$mid&t=$t&h=".md5($uid.$mid."watup".$t)."&cb=".$_GET['cb'];

$rm=array();
if($uid==0) die();
$rm[]=array("Reward"=>"-","Item"=>"My Redemption History","picid"=>$link);
$select ="select aes_decrypt(code,'supersimple') as Reward, concat('',b.name) as Item from reward_codes a join rewards b on a.reward_id=b.id where a.rewarded_to_uid=$uid order by a.id desc";
$rewards=db::rows($select);

//$rewards=array();
$select="select concat('Earned Points: ',points_earned) as Reward, 'Loaded Picture' as Item,id as picid from UploadPictures where uid=$uid";
$pts=db::rows($select);

$select="select concat('Earned XP: ',xp) as Reward, event as Item from pr_xp where uid=$uid";
$xp=db::rows($select);



die(json_encode(array_merge($rm,$rewards,$pts,$xp)));
