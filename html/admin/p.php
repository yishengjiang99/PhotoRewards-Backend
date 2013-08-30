<?php
date_default_timezone_set("UTC");
echo date("Y-m-d H:i:s");
require_once("/var/www/lib/functions.php");

$rev1=db::rows("select sum(revenue)/100 as rev from sponsored_app_installs where created>date_sub(now(), interval 1 hour)");
$ppl1 =db::rows("select sum(amount)/100 as ppal_out from PaypalTransactions where created>date_sub(now(), interval 1 hour) and status='processed'");
$giftcards1=db::rows("select sum(r.Points)/1000 as cards_out from reward_codes a join rewards r on a.reward_id=r.id where given_out=1 and date_redeemed>date_sub(now(), interval 1 hour)");

$rev=db::rows("select sum(revenue)/100 as rev from sponsored_app_installs where created>date_sub(now(), interval 1 day)");
$ppl24 =db::rows("select sum(amount)/100 as ppal_out from PaypalTransactions where created>date_sub(now(), interval 1 day) and status='processed'");
$giftcards24=db::rows("select sum(r.Points)/1000 as cards_out from reward_codes a join rewards r on a.reward_id=r.id where given_out=1 and date_redeemed>date_sub(now(), interval 1 day)");

$revall=db::rows("select sum(revenue)/100 as rev from sponsored_app_installs");
$giftcardsall=db::rows("select sum(r.Points)/1000 as cards_out from reward_codes a join rewards r on a.reward_id=r.id where given_out=1");

$revenue_breakout=db::rows("select sum(revenue)/100 as revenue, network from sponsored_app_installs where created>date_sub(now(), interval 1 day) group by network");
$revenue_breakout_all=db::rows("select sum(revenue)/100 as rev, network from sponsored_app_installs group by network order by rev");
$ppoutall=db::rows("select sum(amount)/100 as pplout from PaypalTransactions where status='processed'");
$newusers_today=db::rows("select avg(stars),sum(stars), avg(xp), count(1) as cnt, app from appuser where created>date_sub(now(), interval 1 day) group by app");
$refhour=db::rows("select sum(ltv)/100 as revenue,sum(points_to_agent)*2/1000 as cost,count(1) as joiners,count(distinct agentUid) as agent from appuser a join referral_bonuses b
 on a.id=b.joinerUid where a.created>date_sub(now(), interval 4 hour)");
$reffday=db::rows("select sum(ltv)/100 as revenue,sum(points_to_agent)*2/1000 as cost,count(1) as joiners,count(distinct agentUid) as agent from appuser a join referral_bonuses b 
 on a.id=b.joinerUid where a.created>date_sub(now(), interval 1 day)");

$refweek=db::rows("select sum(ltv)/100 as revenue,sum(points_to_agent)*2/1000 as cost,count(1) as joiners,count(distinct agentUid) as agent from appuser a join referral_bonuses b 
 on a.id=b.joinerUid where a.created>date_sub(now(), interval 7 day)");

$allusers=db::rows("select app, avg(stars),sum(if(modified>date_sub(now(), interval 1 day),1,0)) as active_today, avg(xp),count(1) as cnt,sum(stars)/1000 as account_payable, app from appuser where banned=0 and app='picrewards'");
$cashouts=db::rows("select b.name, b.Points,sum(given_out),count(1) from reward_codes a join rewards b on a.reward_id=b.id group by a.reward_id");
$referrals=db::rows("select count(1), sum(points_to_agent)/1000 as paid_to_agent,sum(points_to_agent)/1000 as paid_to_agent,sum(points_to_joiner)/1000 as paid_to_joiner, count(distinct agentUid) as agents from referral_bonuses");
$ppal=db::rows("select sum(amount),count(1),count(distinct transfer_to_user_id) as unique_users from PaypalTransactions");
$rolling7user=db::rows("select date_format(created,'%Y-%m-%d') as date, count(1) as new, sum(if(modified>date_sub(now(), interval 1 day), 1, 0)) as activetoday,avg(ltv) as ltv ,sum(if(ltv>0,1,0)) as monetized, sum(if(modified>date_sub(now(), interval 1 day), 1, 0))/count(1) as attribution, avg(stars)/10 as balance from appuser where app='picrewards' and created>date_sub(now(), interval 7 day) and banned=0 group by datediff(now(),created) order by created desc");
$topagents=db::rows("select count(1), count(distinct ipaddress), avg(ltv) as ltv, avg(stars) as balance,agentUid from appuser a join referral_bonuses b on a.id=b.joinerUid where b.created>date_sub(now(), interval 1 day) group by agentUid having count(1)>1");
$duamua=db::rows("select sum(if(modified>date_sub(now(), interval 1 day),1,0))/sum(if(modified>date_sub(now(), interval 27 day),1,0)) as DoMu from appuser where app='picrewards' and banned=0");
$namesavail=db::row("select count(1) as cnt from available_nicknames where taken=0");
$namesleft=$namesavail['cnt'];
$devices=db::rows("select substring_index(deviceInfo,';|',1) as device, count(1) as cnt, avg(ltv) as ltv from appuser where deviceInfo!='' and banned=0 group by substring_index(deviceInfo,'|',1) having cnt>40");
$pso=db::rows("select type,count(1) as cnt, count(distinct uid) as uniq_users, sum(points_earned) as pts from UploadPictures where created>date_sub(now(), interval 24 hour) group by type");
$spins=db::rows("select count(distinct uid) as du,count(1) as spin, sum(win) as win from spins where created>date_sub(now(), interval 1 day) and uid!=2902");
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
echo "<tr><td colspan=3>P&L All Time</td></tr>";
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
echo "psource-24";
echo rows2table($pso);
//echo "spin";
//echo rows2table($spins);
echo "</td><td valign=top>";
echo "all users";
echo rows2table($allusers);
echo "<table border=1>";
echo "<tr><td colspan=2>Network Performances</td></tr>";
echo "<tr><td>Today</td><td>Alltime</td></tr>";
echo "<tr><td valign=top>".rows2table($revenue_breakout)."</td>";
echo "<td valign=top>".rows2table($revenue_breakout_all)."</td></tr></table>";
echo "Inventory";
echo rows2table($cashouts);
echo "devices";
echo rows2table($devices);
$countries=db::rows("select avg(ltv),country,count(1) as cnt, sum(if(modified>date_sub(now(), interval 1 day),1,0)) as activetoday from appuser where banned=0 and ipaddress!='' group by country  having count(1)>20 order by count(1) desc");
echo "countries";
echo rows2table($countries);
echo "<li>nicknames left: $namesleft";
echo "</td></tr></table>";

?>
