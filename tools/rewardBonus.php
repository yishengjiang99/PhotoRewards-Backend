<?php
exit;
require_once('/var/www/lib/functions.php');
require_once('/var/www/html/pr/apns.php');

$users=db::rows("select a.*,b.agentUid from appuser a join referral_bonuses b on a.id=b.joinerUid where points_to_agent=0 and a.inviter_id=0 and b.created>date_sub(now(), interval 1 hour)");

foreach($users as $user){
 $uid=$user['id'];
 $inviterId=$user['agentUid'];
 _dbexec("update appuser set inviter_id=$inviterId where id=$uid");
}

$users=db::rows("select * from appuser where inviter_id!=0 and banned=0 and has_entered_bonus!=1 and modified>date_sub(now(), interval 5 hour) and fbid>0 and fbfriends>3 and (ltv>20 || stars>=450 || visit_count>90)");
foreach($users as $user){
 
 if(strpos($user['deviceInfo'],'5_')!==false){
    db::exec("update appuser set has_entered_bonus=1 where id=$uid limit 1");
    continue;
 }
 
 $inviterId=$user['inviter_id'];
 $stars=$user['stars'];
 $username=$user['username'];
 $uid=$user['id'];
 $bonus=db::row("select * from referral_bonuses where joinerUid=$uid and agentUid=$inviterId");
 if(!$bonus){
   db::exec("update appuser set has_entered_bonus=1 where id=$uid limit 1");
 //  continue;
   $creation=$user['created'];
   _dbexec("insert into referral_bonuses set joinerUid=$uid, agentUid=$inviterId,created='$creation',points_to_agent=0,points_to_joiner=300");
   $bonus=db::row("select * from referral_bonuses where joinerUid=$uid and agentUid=$inviterId");
 }
  if(!$bonus) {
   echo "insert fail";
	continue;
 }
 if($bonus['points_to_agent']!=0){
   db::exec("update appuser set has_entered_bonus=1 where id=$uid limit 1");
   continue;
 }
 $agent=db::row("select * from appuser where id=$inviterId");
 if($agent['banned']==1) {
   _dbexec("update appuser set has_entered_bonus=1 where id=$uid");
  continue;
 }
 if($user['ltv']<60 && $user['stars']<500 && $user['xp']<10) cotninue;
 $bonusId=$bonus['id'];
 $agentBonus=$bonus['points_to_joiner']*4;
 _dbexec("update appuser set stars=stars+$agentBonus where id=$inviterId");
 _dbexec("update appuser set has_entered_bonus=1 where id=$uid");
 _dbexec("update referral_bonuses set points_to_agent=$agentBonus where id=$bonusId");
 $inviter=db::row("select * from appuser where id=$inviterId");
 $code=$inviter['username'];
 apnsUser($inviterId,"$username entered your bonus code '$code' for $agentBonus points!");
 apnsUser(2902,"$username entered your bonus code '$code' for $agentBonus points!");
}

function _dbexec($sql){
echo "\n$sql";
 db::exec($sql);
}
