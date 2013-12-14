<?php
require_once("/var/www/lib/functions.php");
require_once("/var/www/lib/firewall.php");
$uid=intval($_GET['uid']);
$mid=md5($idfa);
$t=time();
$h=md5($uid.$mid."watup".$t);
$link="$uid&r=r&mid=$mid&t=$t&h=".md5($uid.$mid."watup".$t)."&cb=".$_GET['cb'];

if($uid==0) die();
$user=db::row("select * from appuser where id=$uid");

if($user['banned']==1){
 die(json_encode(array("Reward"=>"Account under review","Item"=>"Please email support@photorewards.net")));
}

$history=array();
$history[]=array("Reward"=>"Click to see the gift cards","Item"=>"My Redemption History","picid"=>$link);
$select ="select b.id, unix_timestamp(a.date_redeemed) as t, date_format(a.date_redeemed,'  (%Y/%m/%d)') as datestr, aes_decrypt(code,'supersimple') as Reward, concat('Redeemed: ',b.name) as Item from reward_codes a join rewards b on a.reward_id=b.id where a.rewarded_to_uid=$uid";
$gc=db::rows($select);
foreach($gc as &$g){
  $g['Reward']=$g['Reward'];
  $history[$g['t']]=$g;
}

$select ="select status, unix_timestamp(created) as t, date_format(created,'  (%Y/%m/%d)') as datestr, amount from PaypalTransactions where transfer_to_user_id=$uid";
$ppal=db::rows($select);
foreach($ppal as &$g){
  $dollar=money_format('$%i', (double)$g['amount']/100);
  if($g['status']=='processed'){
    $g['Reward']="Processed ".$g['datestr'];
  }else{
    $g['Reward']="Pending Review ".$g['datestr'];
  }
  $g['Item']="Cashed out ".$dollar." on PayPal";
  $history[$g['t']]=$g;
}

$select="select b.Name,offer_id,title, unix_timestamp(a.created) as t, date_format(a.created,'  (%Y/%m/%d)') as datestr, concat('Earned Points: ',points_earned) as Reward, 'Shared a picture' as Item,b.id as picid from UploadPictures a left join apps b on a.offer_id=b.id where uid=$uid and points_earned>0";
$pictures=db::rows($select);
foreach($pictures as &$g){ 
  $title=$g['title'];
  if($g['offer_id']!=0){
    $title=$g['Name'];
    unset($g['title']);
  }
  $g['Item']="Upoloaded a Picture: ".$title;
  $g['Reward']=$g['Reward']." ".$g['datestr'];
  $history[$g['t']]=$g;
}

$refagaent ="select b.username, b.xp,b.stars,b.has_entered_bonus,a.points_to_agent,unix_timestamp(a.created) as t, date_format('%Y-%m-%d',a.created) as datestr, concat(username, \" joined.\") as Item, concat(points_to_agent,\" points earned\") as Reward from referral_bonuses a join appuser b on a.joinerUid=b.id where agentUid=$uid order by a.created desc";
$refagaent=db::rows($refagaent);

foreach($refagaent as &$g){
  $g['Item']=$g['Item']." ".$g['datestr'];
  if($g['has_entered_bonus']==2 || $g['points_to_agent']==0){
    $needs=300-$g['stars'];
    $g['Reward']="Joiner needs to earn $needs more points";
  }
  $history[$g['t']]=$g;  
}

$select="select unix_timestamp(a.created) as t, date_format(a.created,'  (%Y/%m/%d)') as datestr, concat('Earned XP: ',xp) as Reward, event as Item from pr_xp a where uid=$uid and xp>0 order by id desc limit 100";
$xp=db::rows($select);
foreach($xp as &$g){
 $g['Reward'].=" ".$g['datestr'];
 $history[$g['t']]=$g;
}
krsort($history);
$out=array();
foreach($history as $h){
  $out[]=$h;
}
die(json_encode($out));
