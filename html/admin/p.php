<html>
<body>
<?php
date_default_timezone_set("UTC");
echo date("Y-m-d H:i:s");
echo "<br><a href=/admin/trends.php>Trends</a>";
echo "<br><a href=/admin/redemptions.php>REdemptions</a>";
echo "<br><a href=/admin/refall.php>Referrals</a>";
echo "<br>..<a href=/admin/banned.php>Banned Users</a>";
echo "<br>.<a href=/admin/conversions.php>Conversions</a>";

require_once("/var/www/lib/functions.php");

$rev1=db::rows("select sum(revenue)/100 as rev from sponsored_app_installs where created>date_sub(now(), interval 1 hour)");
$ppl1 =db::rows("select sum(amount)/100 as ppal_out from PaypalTransactions where created>date_sub(now(), interval 1 hour) and status='processed'");
$giftcards1=db::rows("select sum(r.Points)/1000 as cards_out from reward_codes a join rewards r on a.reward_id=r.id where given_out=1 and date_redeemed>date_sub(now(), interval 1 hour)");

$rev=db::rows("select sum(revenue)/100 as rev from sponsored_app_installs where created>date_sub(now(), interval 1 day)");
$ppl24 =db::rows("select sum(amount)/100 as ppal_out from PaypalTransactions where created>date_sub(now(), interval 1 day) and status='processed'");
$giftcards24=db::rows("select sum(r.Points)/1000 as cards_out from reward_codes a join rewards r on a.reward_id=r.id where given_out=1 and date_redeemed>date_sub(now(), interval 1 day)");

$revall=db::rows("select sum(revenue)/100 as rev from sponsored_app_installs where created>date_sub(now(), interval 1 month)");
$giftcardsall=db::rows("select sum(r.Points)/1000 as cards_out from reward_codes a join rewards r on a.reward_id=r.id where given_out=1 and date_redeemed>date_sub(now(), interval 1 month)");

$revenue_breakout=db::rows("select sum(revenue)/100 as revenue,count(1) as cnt, sum(Amount)/1000 as up,network from sponsored_app_installs where created>date_sub(now(), interval 1 day) group by network order by revenue desc");
$revenue_breakout_30=db::rows("select sum(revenue)/100 as rev, count(1) as cnt,sum(Amount)/1000 as up,network from sponsored_app_installs where created>date_sub(now(), interval 30 day) 
group by network order by rev desc");
$revenue_hour_breakout=db::rows("select sum(revenue)/100 as revenue,count(1) as cnt,sum(Amount)/1000 as up, network from sponsored_app_installs where created>date_sub(now(), interval 1 hour) group by network order by revenue desc");
$revenue_mtd=db::rows("select sum(revenue)/100 as rev,count(1) as cnt,sum(Amount)/1000 as up, network from sponsored_app_installs where month(created)>=month(now()) group by network order by rev desc");
$ppoutall=db::rows("select sum(amount)/100 as pplout from PaypalTransactions where status='processed' and created>date_sub(now(), interval 1 month)");
$newusers_today=db::rows("select avg(stars),sum(stars), avg(xp), count(1) as cnt, app from appuser where created>date_sub(now(), interval 1 day) group by app");
$refhour=db::rows("select sum(ltv)/100 as revenue,sum(points_to_agent)*2/1000 as cost,count(1) as joiners,count(distinct agentUid) as agent from appuser a join referral_bonuses b
 on a.id=b.joinerUid where a.created>date_sub(now(), interval 4 hour)");
$reffday=db::rows("select sum(ltv)/100 as revenue,sum(points_to_agent)*2/1000 as cost,count(1) as joiners,count(distinct agentUid) as agent from appuser a join referral_bonuses b 
 on a.id=b.joinerUid where a.created>date_sub(now(), interval 1 day)");

$reffutc=db::rows("select sum(ltv)/100 as revenue,sum(points_to_agent)*2/1000 as cost,count(1) as joiners,count(distinct agentUid) as agent from appuser a join referral_bonuses b
 on a.id=b.joinerUid where a.created>CURDATE()");

$refweek=db::rows("select sum(ltv)/100 as revenue,sum(points_to_agent)*2/1000 as cost,count(1) as joiners,count(distinct agentUid) as agent from appuser a join referral_bonuses b 
 on a.id=b.joinerUid where a.created>date_sub(now(), interval 7 day)");

$allusers=db::rows("select app, avg(stars),sum(if(created>date_sub(now(), interval 1 day),1,0)) as newtoday, sum(if(modified>date_sub(now(), interval 1 day),1,0)) as active_today, avg(xp),count(1) as cnt,sum(stars)/1000 as AP, app from appuser where banned=0 and app in ('contest','picrewards') group by app having count(1)>10");
$cashouts=db::rows("select b.id,b.name, b.Points,sum(given_out) as o,count(1) as cnt from reward_codes a join rewards b on a.reward_id=b.id group by a.reward_id");
$referrals=db::rows("select count(1), sum(points_to_agent)/1000 as paid_to_agent,sum(points_to_agent)/1000 as paid_to_agent,sum(points_to_joiner)/1000 as paid_to_joiner, count(distinct agentUid) as agents from referral_bonuses");
$ppal=db::rows("select sum(amount),count(1),count(distinct transfer_to_user_id) as unique_users from PaypalTransactions where created>date_sub(now(), interval 1 month)");
$rolling7user=db::rows("select date_format(created,'%Y-%m-%d') as date, count(1) as new, sum(if(banned=1, 1, 0)) as banned,sum(if(modified>date_sub(now(), interval 1 day), 1, 0)) as activetoday,
floor(avg(ltv)) as ltv ,
sum(if(ltv>0,1,0)) as monetized, 
sum(if(modified>date_sub(now(), interval 1 day), 1, 0))/sum(if(banned!=1, 1, 0))  as pactive, floor(avg(stars)/10) as balance from appuser where app='picrewards' and created>date_sub(now(), interval 14 day) group by datediff(now(),created) order by created desc");
$topsql="select c.username, c.banned as b,count(1) as nj,sum(points_to_agent) as pnts, count(distinct a.ipaddress) as ips, floor(avg(a.ltv)) as ltv,agentUid as uid, 
concat('/admin/ban.php?uid=',c.id) as linkban, concat('/admin/unban.php?uid=',c.id) as linkunb,concat('/admin/ref.php?uid=',c.id) as linkd
from appuser a join referral_bonuses b on a.id=b.joinerUid join appuser c on b.agentUid=c.id 
where b.created>date_sub(now(), interval 1 day) and joinerUid!=2902 group by agentUid having count(1)>2";
$topagents=db::rows($topsql);
$duamua=db::rows("select sum(if(modified>date_sub(now(), interval 1 day),1,0))/sum(if(modified>date_sub(now(), interval 27 day),1,0)) as DoMu from appuser where app='picrewards' and banned=0");
$namesavail=db::row("select count(1) as cnt from available_nicknames where taken=0");
$namesleft=$namesavail['cnt'];
$devices=db::rows("select substring_index(deviceInfo,';|',1) as device, count(1) as cnt, avg(ltv) as ltv,sum(if(created>date_sub(now(), interval 1 day),1,0)) as newtoday,sum(if(modified>date_sub(now(), interval 1 day),1,0)) as activetoday from appuser where deviceInfo!='' and banned=0 group by substring_index(deviceInfo,'|',1) having cnt>40");
$pso=db::rows("select type,count(1) as cnt, count(distinct uid) as uniq_users, sum(points_earned) as pts from UploadPictures where created>date_sub(now(), interval 24 hour) group by type");
$spins=db::rows("select count(distinct uid) as du,count(1) as spin, sum(win) as win from spins where created>date_sub(now(), interval 1 day) and uid!=2902");
$appdog=db::rows("select count(1) as cnt, avg(ltv) as ltv, sum(if(ltv>0,1,0)) as monetized,sum(if(banned>0,1,0)) as banned from appuser where source='appdog'");
$contestrev=db::rows("select sum(revenue)/100 as rev,count(1) as cnt from sponsored_app_installs a join appuser b on a.uid=b.id where b.app='contest' and a.created>date_sub(now(), interval 1 day)");
$contestClicks=db::rows("select count(1) as clicks, count(distinct uid) as users from contest_clicks where created>date_sub(now(), interval 1 day)");

echo "<table><tr><td valign=top>"; //lev1
echo "<table border=1>";
echo "<tr><td colspan=3>P&L last hour</td>";
echo "<td>referrals 4 last hour</td></tr>";
echo "<tr><td>".rows2table($rev1)."</td>";
echo "<td>".rows2table($ppl1)."</td>";
echo "<td>".rows2table($giftcards1)."</td>";
echo "<td>".rows2table($refhour)."</td>";
echo "<td>".rows2table($duamua)."</td>";
echo "</tr></table>";
echo "<table><tr><td>";
echo "<table border=1>";
echo "<tr><td colspan=3>P&L last 24</td></tr>";
echo "<tr><td>".rows2table($rev)."</td>";
echo "<td>".rows2table($ppl24)."</td>";
echo "<td>".rows2table($giftcards24)."</td></tr></table>";
echo "</td><td>";
echo "<table border=1>";
echo "<tr><td colspan=3>P&L last 30 days</td></tr>";
echo "<tr><td>".rows2table($revall)."</td>";
echo "<td>".rows2table($giftcardsall)."</td>";
echo "<td>".rows2table(	$ppoutall)."</td></tr></table>";
echo "</td></tr></table>";
echo "<table border=1>";
echo "<tr><td>ref-24</td><td>ref-week</td></tr>";
echo "<tr>";
echo "<td>".rows2table($reffday)."</td>";
echo "<td>".rows2table($refweek)."</td>";
echo "</tr></table>";
echo "r7";
echo rows2table($rolling7user);
echo "top agents today";
echo rows2table($topagents);
echo "<table><tr><td>";
echo "psource-24";
echo rows2table($pso);
echo "</td><td>";
echo "spin";
echo rows2table($spins);
echo "</td></tr></table>";
$s2=db::rows("select count(distinct uid) as users, sum(revenue)/100 as rev, network,count(1) as cnt from sponsored_app_installs where created>date_sub(now(), interval 24 hour) and sub2!='' group by network");
echo "<table><tr><td>";
echo "s2".rows2table($s2);
echo "</td><td>";
$fb=db::rows("select count(1) as fbid, sum(if(email!='',1,0)) as emails from appuser where app='picrewards' and fbid!=0");
echo rows2table($fb);
$activeusers="select substring_index(deviceInfo,'|',-1) as v, count(1) as cnt,sum(if(created>date_sub(now(), interval 1 day),1,0)) as new, 
sum(if(created>date_sub(now(), interval 1 day),ltv,0))/sum(if(created>date_sub(now(), interval 1 day),1,0)) as ltvnew,
 avg(ltv) as ltv from appuser where deviceInfo!='' and modified>date_sub(now(), interval 7 day) and app='picrewards' and banned=0 group by deviceInfo like '%6_%',deviceInfo like '%7_%'";
echo rows2table(db::rows($activeusers));
echo "</td></tr></table>";
echo "</td><td valign=top>";
echo "<table border=1><tr><td colspan=2>Photo Contest last 24</td><tr><td>";
echo rows2table($contestrev);
echo "</td><td>";
echo rows2table($contestClicks);
echo "</td></tr></table>";
echo "all users";
echo rows2table($allusers);
echo "<table border=1>";
echo "<tr><td colspan=2>Network Performances</td></tr>";
echo "<tr><td valign=top>last 24-hours".rows2table($revenue_breakout)."mtd:".rows2table($revenue_mtd)."</td>";
echo "<td valign=top>Last hour".rows2table($revenue_hour_breakout)."last 30".rows2table($revenue_breakout_30)."</td></tr></table>";
echo "Inventory";
echo "<br>Paypal balance: $".file_get_contents("/var/www/cache/pbal.txt");
echo rows2table($cashouts);
echo "devices";
echo rows2table($devices);
echo "appdog";
echo rows2table($appdog);
$countries=db::rows("select avg(ltv),country,count(1) as cnt, sum(if(created>date_sub(now(), interval 1 day),1,0)) as newtoday,sum(if(modified>date_sub(now(), interval 1 day),1,0)) as activetoday from appuser where banned=0 and ipaddress!='' group by country order by count(1) desc limit 10");
echo "countries";
echo rows2table($countries);
echo "<li>nicknames left: $namesleft";
echo "</td></tr></table>";
?>
</body>
</html>
