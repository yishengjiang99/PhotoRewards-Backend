<?php
require_once("/var/www/lib/functions.php");
require_once("/var/www/lib/apns.php");
$dorm=db::rows("select id,created,modified from appuser where app='picrewards' and created=modified and xp=0 and stars=0 and created>date_sub(now(), interval 1 day) and banned=0 and visit_count<2
 and created<date_sub(now(), interval 10 minute)");
$dorm=db::rows("select id,created,modified from appuser where app='picrewards' and xp<5 and ltv=0 and created>date_sub(now(), interval 1 day) and visit_count<2 and created<date_sub(now(), interval 10 minute)");
foreach($dorm as $d){
  $uid=$d['id'];
  $update="update appuser set visit_count=visit_count+1 where id=$uid";
  echo "\n$update";
  db::exec($update);
  apnsUser($uid,"PhotoRewards: Upload Pictures, Earn Free Gift Cards (Amazon.com, PayPal, Starbucks, xBox)");
}
