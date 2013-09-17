<?php
require_once('/var/www/lib/functions.php');
$uid="";
$registered=1;
$mac=$_GET['mac'];
$idfa=$_GET['idfa'];
$uid=$_GET['uid'];
if(!$mac || !$idfa || !$uid){
 $mac=$_COOKIE['mac'];
 $idfa=$_COOKIE['idfa'];
 $uid=$_COOKIE['uid'];
}
if(!$mac || !$idfa ||$uid){
 $registered=0;
}else{
 setcookie("mac",$mac,time()+60*60*24*30*12);
 setcookie("idfa",$idfa,time()+60*60*24*30*12);
 setcookie("uid",$uid,time()+60*60*24*30*12);
}
$msg="";
$offerId=$_GET['oid'];
$voted=0;
if(isset($_GET['h']) && isset($_GET['vote'])){
 $h=$_GET['h'];
 $vote=$_GET['vote'];
 if($h != md5($vote."dfadf")) die('pwned');
 $hh=md5($uid.$offerId);
 
 $v=db::row("select * from once where h='$hh'");
 if($v){
   $msg="You already voted!";
   $voted=1;
 }else{
  db::exec("insert ignore into once set h='$hh'");
  db::exec("update UploadPictures set liked=liked+1 where id='$vote'");
error_log("update UploadPictures set liked=liked+1 where id='$vote'");
  $msg="Your voice has been heard!";
 }
}

$offerId=$_GET['oid'];
$subid=$uid.",".$offerId;
$getapp="http://www.json999.com/redirect.php?from=context$subid";
if($registered==1){
 $getapp="photorewards://";
} 
$offer=db::row("select name, storeID,click_url as RedirectURL from offers where id=$offerId");
 $offer['RedirectURL']=str_replace("SUBID_HERE",$subid,$offer['RedirectURL']);
 $offer['RedirectURL']=str_replace("IDFA_HERE",$idfa,$offer['RedirectURL']);
 $offer['RedirectURL']=str_replace("MAC_HERE",$mac,$offer['RedirectURL']);
$offer['RedirectURL']="http://www.json999.com/pr/click.php?subid=$subid&go=".urlencode($offer['RedirectURL']);

 $click=$offer['RedirectURL'];
 
if($registered==0){
 $votebase=$getapp;
// $click=$getapp;
}
$appid=$offer['storeID'];
$name=$offer['name'];
$pictures=array();
if(isset($_GET['dlink'])){
  $dlink=$_GET['dlink'];
  $sql="select shared, title, a.id, type, liked, a.uid, points_earned,username, fbid from UploadPictures a left join appuser b on a.uid=b.id where a.id='$dlink'";
  $pictures=array_merge($pictures, db::rows($sql));
}
  $sql="select shared, title, a.id, type, liked, a.uid, points_earned,username, fbid from UploadPictures a left join appuser b on a.uid=b.id where offer_id='$appid' and reviewed=1 order by RAND() desc";
  $pictures=array_merge($pictures, db::rows($sql));
?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
     <meta content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport">
    <meta charset="utf-8">
    <title>Picture Contest from PhotoRewards</title>
    <link href="/css/bootstrap.css" rel="stylesheet">
    </head>
  
    <body>
  <div style='max-width:640px'>
   <h1>Picture Contest</h1>
   <?php echo $msg; ?>
   <i>Contest ends at 11:59pm July 24th, 2013.</i><br><h3>Whoever gets most votes gets a <a href='http://i.imgur.com/wEq4jFGl.jpg'>FREE $19.99 GIFT CARD FOR xBOX</a></h3>
  <br>No purchase necessary<br><b>To Enter</b>: <a href='<?php echo $click ?>'>Try the App</a>, then upload a GREAT screenshot from <a href='<?php echo $getapp ?>'>PhotoRewards</a> to win.
 
 <?php 
   foreach($pictures as $p){
   $id=$p['id'];
   $voter="/pr/contest.php?uid=$uid&oid=$offerId&vote=$id&h=".md5($id."dfadf");
   $direct="http://www.json999.com/pr/contest.php?oid=$offerId&dlink=$id";
   $link="http://www.json999.com/pr/uploads/".$p['id'].".jpeg";
   echo "<div style='margin-top:10px;min-height:150px'><table><tr>";
   echo "<td valign=top><img width=260 src='$link'>";
   echo "<br><a href='https://twitter.com/intent/tweet?text=".urlencode("Vote for this picture on #PhotoRewards: $direct")."'>Tweet</a>";
   echo "</td>";
   echo "<td valign=top>From <b>".$p['username']."</b><br>(<b>".$p['points_earned']." Points Earned</b>)";
  if(intval($p['fbid'])){
    echo "<img width=40 src='https://graph.facebook.com/".$p['fbid']."/picture?width=200&height=200'>";
  }
   echo "<br>Votes: ".$p['liked'];
   if($voted==0) echo "<br><a class='btn' href='$voter' class=btn>Vote This One</a>";
   echo "<br><a style='margin-top:10px' class='btn' href='$click'>Try The App</a>";
   echo "</td></tr></table></div>";
  }
?>
</div>
    </body>
</html>
