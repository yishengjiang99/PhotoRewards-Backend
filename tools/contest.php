<?php
require_once("/var/www/lib/functions.php");
$rewards="select CashValue,count(1) as cnt, count(distinct uid) as users,contest_id,c.name,c.id as rid from contest_entry a join contest b on a.contest_id=b.id join rewards c on b.reward_id=c.id where a.created >date_sub(now(), interval 3 day) group by contest_id order by count(distinct uid) desc limit 8";
$rewards=db::rows($rewards);
foreach($rewards as $i=>$reward){
    $cashValue=intval($reward['CashValue']);
    $duration=$cashValue*9;
    $name=$reward['name'];
    $rewardId=$reward['rid'];
    $active=db::row("select * from contest where reward_id=$rid and status=1");
    if($active) continue;
    $insert="insert into contest set name='$name',reward_id=$rewardId,created=now(),contest_ends=date_add(now(), interval $duration hour),status=1,storeId=0";
    email("yisheng@grepawk.com","New Contest $name for $duraton hours",$insert);
    db::exec($insert);
}
