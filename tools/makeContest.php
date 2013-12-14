<?php
require_once("/var/www/lib/functions.php");


$offerId=91;
$rewardId=12;

$activeOffers=db::rows("select a.id as offer_id,active, affiliate_network, b.IconURL, click_url as RedirectURL, platform, 'Free' as Cost, dailylimit,completions, a.name as Name,'App' as OfferType,thumbnail,storeID as StoreID,
    cash_value as Amount, a.description as Action,completions,geo_target as geo from offers a left join apps b on a.storeID=b.id where a.id=$offerId");
foreach($activeOffers as $i=>$offer){
    $id=$offer['offer_id'];
    $running=db::row("select * from contest where status=1 and offer_id=$id");
    if($running) continue;
    $name=$offer['Name'];
    $completion=$offer['completions'];
    $duration=72;
    $insert="insert into contest set name='$name',reward_id=$rewardId,created=now(),contest_ends=date_add(now(), interval $duration hour),status=1,offer_id=$id,storeId=".$offer['StoreID'];
    db::exec($insert);
}
