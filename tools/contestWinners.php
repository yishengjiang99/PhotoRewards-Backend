<?php
require_once("/var/www/lib/functions.php");
$done=db::rows("select id,reward_id from contest where status=1 and contest_ends<now()");
foreach($done as $c){
  $cid=$c['id'];
  $rid=$c['reward_id'];
  $contest=db::row("select a.points,b.email,b.username,b.id from contest_entry a join appuser b on a.uid=b.id where contest_id=$cid and b.banned=0 and b.app='contest' order by points desc limit 1");
  $email=$contest['email'];
  $uid=$contest['id'];
  $points=$contest['points'];
  $update="update contest set status=2 where id=$cid";
 db::exec($update);
  if($points==0) continue;
  $insert="insert into contest_winner set uid=$uid,contest_id=$cid,reward_id=$rid,email='$email',created=now()";
  db::exec($insert);
  echo "\n $cid $rid, $email, $uid, $points";
}
