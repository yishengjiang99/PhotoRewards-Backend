<html>
<body>

<?php
if(isset($_POST['save_offer_detail'])){
$ta=explode(",","name,click_url,thumbnail,cost,description,platform,payout,cash_value,rank_weight,geo_target,active,dailylimit,affiliate_network");
$insert = "insert into offers set";
foreach($_POST as $k=>$v){
 if(!in_array($k,$ta)) continue;
 $insert.=" $k='$v',";
}
$insert=substr_replace($insert,"",-1);
require_once("/var/www/lib/functions.php");
db::exec($insert);
$id=db::lastID();
header("location: edit_offer.php?id=$id");
}
?>
<a href='offers.php'>offerlist</a>
 <form method="POST">
   <input type="hidden" name="save_offer_detail" value="1">
   <input type="hidden" name="offer_id" value="">
   <table class="offer_list_left" width="100%" border="1">
<tr><td class='offer_detail_label' width='20%'>Offer Name</td><td width='80%'><input type='text' name='name' size=70/></td></tr>
<tr><td class='offer_detail_label' width='20%'>Click Thru URL</td><td width='80%'><input type='text' name='click_url' size=70/></td></tr>
<tr><td class='offer_detail_label' width='20%'>Thumbnail URL</td><td width='80%'><input type='text' name='thumbnail' size=70/></td></tr>
<tr><td class='offer_detail_label' width='20%'>Description</td><td width='80%'><input type='text' name='description' size=70/></td></tr>
<tr><td class='offer_detail_label' width='20%'>commission(cents)</td><td width='80%'><input type='text' name='payout' size=70/></td></tr>
<tr><td class='offer_detail_label' width='20%'>payout to user(cents)</td><td width='80%'><input type='text' name='cash_value' size=70/></td></tr>
<tr><td class='offer_detail_label' width='20%'>cost to user(cents)</td><td width='80%'><input type='text' name='cost' size=70/></td></tr>
<tr><td class='offer_detail_label' width='20%'>Weight (higher weight = ranked higher)</td><td width='80%'><input type='text' name='rank_weight' size=70/></td></tr>
<tr><td class='offer_detail_label' width='20%'>Geo Target (comma delimited)</td><td width='80%'><input type='text' name='geo_target' size=70/></td></tr>
<tr><td class='offer_detail_label' width='20%'>platform</td><td width='80%'><input type='text' name='platform' size=70/></td></tr>
<tr><td class='offer_detail_label' width='20%'>Active</td><td width='80%'><input type='text' name='active' size=70/></td></tr>
<tr><td class='offer_detail_label' width='20%'>Daily Limit</td><td width='80%'><input type='text' name='dailylimit' size=70/></td></tr>

<tr><td class='offer_detail_label' width='20%'>Affiliate Network</td><td width='80%'><input type='text' name='affiliate_network' size=70/></td></tr>   </table>
   <input type="submit">
   </form>
</body>
</html>
