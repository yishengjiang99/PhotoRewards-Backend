<html>
<body>
<a href='new_offer.php'>New offer</a>
<?php
require_once("/var/www/lib/functions.php");
$offers=db::rows("select * from offers order by active desc, rank_weight");

echo "<table border=1>";
foreach($offers as $o){
 $id=$o['id'];
 echo "<tr><td><a href='edit_offer.php?id=$id'>Edit Offer</a></td>";
 foreach($o as $k=>$v){
  echo "<td>$v</td>";
 }
 echo "</tr>";
}
?>
</table>
</body>
</html>
