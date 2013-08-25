<?
require_once("/var/www/lib/functions.php");


db::exec("update offers set completion4=0");
db::exec("update offers set completions=0");
$r=db::rows("select count(1) as cnt, offer_id from sponsored_app_installs where created>date_sub(now(), interval 4 hour) group by offer_id");
foreach($r as $rr){
 $cnt=$rr['cnt']; $ref=$rr['offer_id'];
 $update="update offers set completion4=$cnt where id=$ref";
 echo "\n".$update;
 db::exec($update);
}

$r=db::rows("select count(1) as cnt, offer_id from sponsored_app_installs where created>date_sub(now(), interval 24 hour) group by offer_id");
foreach($r as $rr){
 $cnt=$rr['cnt']; $ref=$rr['offer_id'];
 $update="update offers set completions=$cnt where id=$ref";
 echo "\n".$update;
 db::exec($update);
}

$r=db::row("select group_concat(offer_id) as goodever from sponsored_app_installs where network='everbadge' and created>date_sub(now(), interval 6 hour)");
echo $r['gooever'];
file_put_contents("/var/www/html/pr/goodever.json",$r['goodever']);
exit;
$r=db::rows("select count(1) as cnt, refId from UploadPictures where type='UserOffers' group by refId order by count(1) desc");
foreach($r as $rr){
 $cnt=$rr['cnt']; $ref=$rr['refId'];
 $update="update PictureRequests set uploadCount=$cnt where id=$ref";

 echo "\n".$update;
db::exec($update);
}

