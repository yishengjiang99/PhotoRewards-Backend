<html>
<body>

<?php
require_once("/var/www/lib/functions.php");
if(isset($_POST['save_offer_detail'])){
$oid=$_POST['offer_id'];
$o=db::row("select * from offers where id=$oid");
$oldactive=$o['active'];
$newactive=intval($_POST['active']);

$ta=explode(",","name,click_url,thumbnail,description,payout,cash_value,rank_weight,geo_target,active,affiliate_network,platform,cost,storeID,dailylimit");
$insert = "update offers set";
foreach($_POST as $k=>$v){
 if(!in_array($k,$ta)) continue;
 $insert.=" $k='$v',";
}

if($newactive==1 && ($oldactive==0 || $oldactive==1)){
  $insert.="whenlive=now(),";
}
$insert=substr_replace($insert,"",-1);
$insert.=" where id=$oid";
db::exec($insert);
}

$oid=intval($_GET['id']);
$offer=db::row("select * from offers where id=$oid");
?>

<a href='offers.php'>offerlist</a>
 <form method="POST">
   <input type="hidden" name="save_offer_detail" value="1">
   <input type="hidden" name="offer_id" value="<?= $offer['id'] ?>">
   <table class="offer_list_left" width="100%" border="1">
<tr><td class='offer_detail_label' width='20%'>Offer Name</td><td width='80%'><input type='text' name='name' value='<?= $offer['name'] ?>' size=70/></td></tr>
<tr><td class='offer_detail_label' width='20%'>Click Thru URL</td><td width='80%'><input type='text' name='click_url' value='<?= $offer['click_url'] ?>' size=170/></td></tr>
<tr><td class='offer_detail_label' width='20%'>Thumbnail URL</td><td width='80%'><input type='text' name='thumbnail' value='<?= $offer['thumbnail'] ?>' size=170/></td></tr>
<tr><td class='offer_detail_label' width='20%'>Description</td><td width='80%'><input type='text' name='description' value='<?= $offer['description'] ?>' size=170/></td></tr>
<tr><td class='offer_detail_label' width='20%'>commission(cents)</td><td width='80%'><input type='text' name='payout' value='<?= $offer['payout'] ?>' size=70/></td></tr>
<tr><td class='offer_detail_label' width='20%'>payout to user(cents)</td><td width='80%'><input type='text' name='cash_value' value='<?= $offer['cash_value'] ?>'  size=70/></td></tr>
<tr><td class='offer_detail_label' width='20%'>cost to user(cents)</td><td width='80%'><input type='text' name='cost' value='<?= $offer['cost'] ?>' size=70/></td></tr>
<tr><td class='offer_detail_label' width='20%'>Weight (higher weight = ranked higher)</td><td width='80%'><input type='text' value='<?= $offer['rank_weight'] ?>' name='rank_weight' size=70/></td></tr>
<tr><td class='offer_detail_label' width='20%'>Geo Target (comma delimited)</td><td width='80%'><input type='text' name='geo_target' value='<?= $offer['geo_target'] ?>'  size=70/></td></tr>
<tr><td class='offer_detail_label' width='20%'>platform</td><td width='80%'><input type='text' name='platform' value='<?= $offer['platform'] ?>'  size=70/></td></tr>
<tr><td class='offer_detail_label' width='20%'>storeID</td><td width='80%'><input type='text' name='storeID' value='<?= $offer['storeID'] ?>'  size=70/></td></tr>
<tr><td class='offer_detail_label' width='20%'>Active</td><td width='80%'><input type='text' name='active' value='<?= $offer['active'] ?>'  size=70/></td></tr>
<tr><td class='offer_detail_label' width='20%'>Daily Limit</td><td width='80%'><input type='text' name='dailylimit' value='<?= $offer['dailylimit'] ?>'  size=70/></td></tr>

<tr><td class='offer_detail_label' width='20%'>Affiliate Network</td><td width='80%'><input type='text' name='affiliate_network' value='<?= $offer['affiliate_network'] ?>'  size=70/></td></tr>   </table>
   <input type="submit">
   </form>
</body>
</html>
