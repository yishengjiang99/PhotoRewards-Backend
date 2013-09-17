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
$rm[]=array("Reward"=>"Click to see the gift cards","Item"=>"My Redemption History","picid"=>$link);
$select ="select aes_decrypt(code,'supersimple') as Reward, concat('',b.name) as Item from reward_codes a join rewards b on a.reward_id=b.id where a.rewarded_to_uid=$uid order by a.date_redeemed desc";
$rewards=db::rows($select);

//$rewards=array();
$select="select concat('Earned Points: ',points_earned) as Reward, 'Shared a picture' as Item,id as picid from UploadPictures where uid=$uid and points_earned>0 order by created desc";
$pts=db::rows($select);

$refagaent ="select concat(username, \" entered your bonus code\") as Item, concat(points_to_agent,\" points earned\") as Reward 
from referral_bonuses a join appuser b on a.joinerUid=b.id where agentUid=$uid order by a.created desc";

error_log($refagaent);
$refagaent=db::rows($refagaent);



$select="select concat('Earned XP: ',xp) as Reward, event as Item from pr_xp where uid=$uid and xp>0 order by id desc limit 100";
$xp=db::rows($select);

$select="select concat('Won Slots: ',win) as Reward, 'XP Earned' as Item from spins where uid=$uid and win>0 order by id desc limit 100";
$slots=db::rows($select);

die(json_encode(array_merge($rm,$rewards,$refagaent,$pts,$xp,$slots)));
