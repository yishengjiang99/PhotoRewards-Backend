<?php
date_default_timezone_set("UTC");
echo date("Y-m-d H:i:s");
require_once("/var/www/lib/functions.php");
$topsql="select c.username, c.role, c.banned, count(1) as joiners, count(distinct a.ipaddress) as ips, avg(a.ltv) as ltv,agentUid, concat('/admin/ban.php?uid=',c.id) as linkban, concat('/admin/unban.php?uid=',c.id) as linkunban 
from appuser a join referral_bonuses b on a.id=b.joinerUid join appuser c on b.agentUid=c.id 
where b.created>date_sub(now(), interval 30 day) group by agentUid having count(1)>2";
$topagents=db::rows($topsql);
echo $topsql;
echo rows2table($topagents);
?>
