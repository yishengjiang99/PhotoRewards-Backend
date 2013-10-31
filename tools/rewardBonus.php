<?php
require_once('/var/www/lib/functions.php');
require_once('/var/www/html/pr/apns.php');
$users=db::rows("select * from appuser where has_entered_bonus=2 and modified>date_sub(now(), interval 1 day) and (ltv>60 || stars>600)");
foreach($users as $user){
 $inviterId=$user['inviter_id'];
 $stars=$user['stars'];
 $username=$user['username'];
 $uid=$user['id'];
 $bonus=db::row("select * from referral_bonuses where joinerUid=$uid and agentUid=$inviterId");
 if(!$bonus) continue;
 if($bonus['points_to_agent']!=0) continue;
 $bonusId=$bonus['id'];
 $agentBonus=$bonus['points_to_joiner']*3;
 _dbexec("update appuser set stars=stars+$agentBonus where id=$inviterId");
 _dbexec("update appuser set has_entered_bonus=1 where id=$uid");
 _dbexec("update referral_bonuses set points_to_agent=$agentBonus where id=$bonusId");
 $inviter=db::row("select * from appuser where id=$inviterId");
 $code=$inviter['username'];
 apnsUser($inviterId,"$username entered your bonus code '$code' for $agentBonus points!","");
}

function _dbexec($sql){
echo "\n$sql";
 db::exec($sql);
}
