<?php
require_once('/var/www/lib/functions.php');
require_once('/var/www/lib/apns.php');

$rr=db::rows("select a.ltv,stars,xp,b.joinerUid,a.id from appuser a join referral_bonuses b on a.id=b.joinerUid where ltv>100 and a.modified<date_sub(now(), interval 5 day) and a.stars<100 and banned=0 and has_entered_bonus=1");
foreach($rr as $r){
//continue;
  $uid=$r['id'];
  $update="update appuser set has_entered_bonus=0 where id=$uid";
  echo "\n$update";
  db::exec($update);
  apnsUser($uid,"You are eligible to enter another bonus code");
}

$rr=db::rows("select id, stars from appuser where stars<500 and stars>10 and banned=0 and ltv>10 and modified<date_sub(now(), interval 3 day) and has_entered_bonus=1");
foreach($rr as $r){
 $points=$r['stars'];$uid=$r['id'];
 apnsUser($uid,"You have $points unredeemed points on PhotoRewards. PayPal cashout start at 750 Points");
}

