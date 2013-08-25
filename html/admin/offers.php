<html>
<body>
<a href='new_offer.php'>New offer</a>
<?php
require_once("/var/www/lib/functions.php");
$offers=db::rows("select id,name,affiliate_network,active,payout,completions as 'completion24',completion4 as 'completion 4hours', platform,rank_weight,click_url from offers order by active>0 desc, rank_weight");
echo "<br><a href=http://json999.com/admin/conversions.php>Conversion</a>";
echo "<table border=1>";
echo "<tr><td></td>";

foreach($offers[0] as $k=>$v){
   if($k=="thumbnail") continue;
   echo "<td>$k</td>";
}
 echo "</tr>";

foreach($offers as $o){
 $id=$o['id'];
 echo "<tr><td><a href='edit_offer.php?id=$id'>Edit Offer</a></td>";
 foreach($o as $k=>$v){
if($k=="thumbnail") continue;
  echo "<td>$v</td>";
 }
 echo "</tr>";
}
?>
</table>
</body>
</html>
