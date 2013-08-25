<?php
require_once('/var/www/lib/functions.php');
$go=urldecode($_GET['go']);
$subid=$_GET['subid'];
$st=explode(",",$subid);
$uid=intval($st[0]);
$offerID=$st[1];
$offer=db::row("select * from offers where id=$offerID");
$appid=$offer['storeID'];
$name=$offer['name'];
$pictures=array();
if(isset($_GET['dlink'])){
  $dlink=$_GET['dlink'];
  $sql="select shared, title, a.id, type, liked, a.uid, points_earned,username, fbid from UploadPictures a left join appuser b on a.uid=b.id where a.id=$dlink";
  $pictures=array_merge($pictures, db::rows($sql));
}
 $sql="select shared, title, a.id, type, liked, a.uid, points_earned,username, fbid from UploadPictures a left join appuser b on a.uid=b.id where offer_id='$storeId' and reviewed=1 order by RAND() desc";
  $pictures=array_merge($pictures, db::rows($sql));
?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="utf-8">
    <title>Picture Context</title>
    <link href="/css/bootstrap.css" rel="stylesheet">
    </head>
    <body>
   <h1>Picture Contest - <?php echo $name ?> </h1>

 <?php foreach($pictures as $p){
   $id=$p['id'];
   $direct="http://www.json999.com/contest.php?subid=$subid&dlink=$id";
   $link="http://www.json999.com/pr/uploads/".$p['id'].".jpeg";
   echo "From ".$p['username']." (".$p['points_earned']." Points Earned)";
   echo "<table><tr>";
   echo "<td><img src='$link'></td>";
   echo "<td>From ".$p['username']." (".$p['points_earned']." Points Earned)";
   echo "<br>Votes: ".$p['liked'];
   echo "<br><a class=xref xref='/pr/vote.php?id=$id' class=btn><h2>Vote for this Picture</h2>";
   echo "<br><a href='https://www.facebook.com/sharer/sharer.php?u=$direct' target='_blank'>Share on Facebook</a>";
   echo "</td></tr></table>";
 ?>
    </body>
</html>




