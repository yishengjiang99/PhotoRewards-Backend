<?
require_once("/var/www/lib/functions.php");

db::exec("update offers set completion4=0");
db::exec("update offers set completions=0");

$r=db::rows("select count(1) as cnt, offer_id from sponsored_app_installs where created>date_sub(now(), interval 4 hour) group by offer_id");
foreach($r as $rr){
 $cnt=$rr['cnt']; 
 $ref=$rr['offer_id'];
 $update="update offers set completion4=$cnt where id=$ref";
 db::exec($update);
}


$r=db::rows("select count(1) as cnt, offer_id from sponsored_app_installs where created>date_sub(now(), interval 24 hour) group by offer_id");
foreach($r as $rr){
 $cnt=$rr['cnt']; $ref=$rr['offer_id'];
 $update="update offers set completions=$cnt where id=$ref";
 db::exec($update);
}
exit;
$r=db::row("select group_concat(distinct appid) as goodever from sponsored_app_installs a join appuser b on a.uid=b.id where a.created>date_sub(now(), interval 10 hour) and network='everbadge' and  deviceInfo like '%iPod%'");
file_put_contents("/var/www/html/pr/goodever_ipod.json",$r['goodever']);

$r=db::row("select group_concat(distinct appid) as goodever from sponsored_app_installs a join appuser b on a.uid=b.id where a.created>date_sub(now(), interval 10 hour) and network='everbadge' and  deviceInfo like '%iPhone%'");
file_put_contents("/var/www/html/pr/goodever_iphone.json",$r['goodever']);

$r=db::row("select group_concat(distinct appid) as goodever from sponsored_app_installs a join appuser b on a.uid=b.id where a.created>date_sub(now(), interval 10 hour) and network='everbadge' and  deviceInfo like '%iPad%'");
file_put_contents("/var/www/html/pr/goodever_ipad.json",$r['goodever']);

