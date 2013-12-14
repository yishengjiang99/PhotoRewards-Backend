<?php
header("location: picrewards://");
exit;
require_once("/var/www/lib/functions.php");

$r=$_REQUEST;
$uid=intval($_GET['uid']);
$h=$r['h'];

if($uid>0 && $h){
 $user=db::row("select * from appuser where id=$uid");
 $idfa=$user['idfa'];
 $h2=md5($uid.$idfa."ddfassffseesfg");
 if($h==$h2){
   $authuid=$uid;
 }else{
   $authuid=0;
 }
}

$uid=$authuid;

$newuser=1;

if($uid!=0){
 $newuser=0;
 setcookie("uid",$uid,time()+36000);
}

$showtut=0;
if($uid>0 && !isset($_COOKIE['tutshown'])){
 $showtut=1;
 setcookie("tutshown",1,time()+360900);
}

$pic=null;
if(isset($r['pid'])){
 $pid=stripslashes($r['pid']);
 $pic=db::row("select compressed,a.id as pid,a.points_earned, b.cash_value as up, a.uid, b.click_url, b.id as oid, b.thumbnail,b.storeID,b.name,b.affiliate_network from UploadPictures a join offers b 
  on a.offer_id=b.storeID where a.id='$pid' and active>0");
}

if(!$pic && isset($r['appid']) && isset($r['network'])){
  $appid=intval($r['appid']);
  $sql="select a.id as pid,a.points_earned, compressed,b.cash_value as up, a.uid, b.click_url, b.id as oid, b.thumbnail,b.storeID,b.name,b.id as offer_id,b.affiliate_network 
  from UploadPictures a join offers b on b.storeID=a.offer_id where b.storeID=$appid and b.active>0 and b.affiliate_network='everbadge' and a.reviewed>=0 order by a.uid=$uid desc limit 1";
  $pic=db::row($sql);
}
if(!$pic && isset($r['oid'])){
   $oid=intval($r['oid']);
   $pic=db::row("select a.id as pid,a.points_earned, compressed, b.cash_value as up, a.uid, b.click_url, b.id as oid, b.thumbnail,b.storeID,b.name,b.id as offer_id,b.affiliate_network from UploadPictures a join offers b on a.offer_id=b.storeID where b.id=$oid and b.active>0 and a.reviewed>=0 order by a.uid=$uid desc limit 1");
}

if(!$pic){
 $pic=db::row("select a.id as pid,a.points_earned, cash_value as up,compressed, a.uid, b.click_url, b.id as oid, b.thumbnail,b.storeID,b.name,b.affiliate_network from UploadPictures a join offers b on a.offer_id=b.storeID
  where b.active>0 and a.reviewed>=0 order by RAND() limit 1");
 $pid=$pic['pid'];
}

$clicklink="";
$e_msg="";
if($r['o']=='liked'){
  if($uid>0){
    if($user && $user['fbid']==0 && isset($r['post_id'])){
         $pt=explode("_",$r['post_id']);
         $fbid=intval($pt[0]);
         if($fbid>0) db::exec("update appuser set fbid=$fbid where id=".$user['id']);
    }
    $liked=db::row("select * from PicturesLiked where liker_uid=$uid and liked_picture_id='$pid'");
    if($liked){
	$e_msg="You already sharing this picture";
    }else{
     $ppfr=2;
     $points=$pic['points_earned']+$ppfr;
     $uploaderId=$pic['uid'];
     if($user['tracking']<=20 || $uid==2902){
      db::exec("update UploadPictures set points_earned=$points,liked=liked+1 where id='$pid'");
      db::exec("insert into PicturesLiked set liker_uid = $uid,extra_points=$ppfr, created=now(),liked_creator_uid=$uploaderId,liked_picture_id='$pid'");
      db::exec("update appuser set tracking=tracking+1, stars=stars+$ppfr where id=$uid");
      if($uid!=$uploaderId){
        db::exec("update appuser set stars=stars+$ppfr where id=$uploaderId");
        $username=$user['username'];
        require_once("/var/www/lib/apns.php");
	$uploader=db::row("select * from appuser where id=$uploaderId");
        $uidfa=$uploader['idfa'];
        $uh2=md5($uploaderId.$uidfa."ddfassffseesfg");
        $newlink=bitlyLink("http://www.json999.com/pr/p22.php?src=apns&h=".$uh2."&uid=$uploaderId&pid=$pid");
        apnsUser($uploaderId,"$username shared your photo to Facebook. You earned an extra $ppfr Points","$username shared your photo to Facebook. You earned an extra $ppfr Points",$newlink);
      }
      $ut=20-$user['tracking']-1;
      $e_msg="You earned $ppfr extra points for sharing this App! Earn more points when your friends download the App! You can share $ut more times today.";
     }else{
       $e_msg="You already shared 20 photos today!";
     }
    }
  } 
  if($uid==0){
     $e_msg="Please download PhotoRewards and earn points for sharing Photos";
  }
  $pic=db::row("select a.id as pid,compressed,cash_value as up, a.points_earned, a.uid, b.click_url, b.id as oid, b.thumbnail,b.storeID,b.name,b.affiliate_network from UploadPictures a join offers b on a.offer_id=b.storeID 
where b.active>0 and a.reviewed>=0 order by RAND() limit 1");
  $pid=$pic['pid'];
}

$points_earned=0;
$clicklink="";
$name="";
$dir="";
if($pic){
 $pid=$pic['pid'];
 $clicklink=$pic['click_url'];
 $offer_id=$pic['offer_id'];
 if(isset($_GET['subid'])){
   $subid=$_GET['subid'];
 }else{   
   if($pic['affiliate_network']=="everbadge"){
	 $subid=$uid.",".$pic['storeID'].",".$pid;
    }else{
	$subid=$uid.",".$pic['oid'].",".$pid;
    }
 }
 $clicklink=str_replace("SUBID_HERE",$subid,$clicklink);
 error_log("click link ".$clicklink);
 $dir="";
 if($pic['compressed']==5) $dir="arch/";
 $clicklink=bitlyLink($clicklink);
 $name=$pic['name'];
 $points_earned=$pic['points_earned'];
 $extra=intval($pic['up'])*10;
 $appid=$pic['storeID'];
}
$title=$name;
$titlet=explode("-",$title);
$title=$titlet[0];
$uploaderId=$pic['uid'];
$points=$info['points_earned'];
$url="https://www.json999.com/pr/p22.php?pid=$pid&subid=$subid";
$dir = $pic['compressed']==5 ? "arch/" : "";
$picture="http://json999.com/pr/uploads/".$dir."$pid.jpeg";
$cb="https://www.json999.com/pr/p22.php?uid=$uid&pid=$pid&h=$h&o=liked";
$tweetmsg="Checkout '$title' on iOS App Store Today!";
$tweet="https://twitter.com/intent/tweet?hashtags=photorewards&url=".urlencode($clicklink)."&text=".urlencode($tweetmsg);
$caption="Checkout this screenshot of '$title' on PhotoRewards. Download $name now from the App Store! $clicklink";
$publish="http://www.facebook.com/dialog/feed?app_id=146678772188121&link=".urlencode($url)."&name=".urlencode($title)."&caption=".urlencode($caption)."&redirect_uri=".urlencode($cb)."&picture=$picture";
?>
<html>
<head>
	<title>PhotoRewards - Screenshot of '<?php echo $title ?>'</title>
<meta property="og:title" content="PhotoRewards - Screenshot of '<?php echo $title ?>'"/>
<meta name = "viewport" content = "user-scalable=no, initial-scale=1.0, maximum-scale=1.0, width=device-width">
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<style>
.btn {
    -moz-border-bottom-colors: none;
    -moz-border-left-colors: none;
    -moz-border-right-colors: none;
    -moz-border-top-colors: none;
    background-color: #F5F5F5;
    background-image: -moz-linear-gradient(center top , #FFFFFF, #E6E6E6);
    background-repeat: repeat-x;
    border-color: #CCCCCC #CCCCCC #BBBBBB;
    border-image: none;
    border-radius: 4px 4px 4px 4px;
    border-style: solid;
    border-width: 1px;
    box-shadow: 0 1px 0 rgba(255, 255, 255, 0.2) inset, 0 1px 2px rgba(0, 0, 0, 0.05);
    color: #333333;
    cursor: pointer;
    display: inline-block;
    font-size: 13px;
    line-height: 18px;
    margin-bottom: 0;
    padding: 4px 10px;
    text-align: center;
    text-shadow: 0 1px 1px rgba(255, 255, 255, 0.75);
    vertical-align: middle;
}
.max {
    margin-left: auto;
    margin-right: auto;
    max-width: 900px;
    overflow: hidden;
}
.content_wrapper {
    background-color: #FFFFFF;
    border-radius: 15px 15px 15px 15px;
    clear: both;
    margin-bottom: 15px;
    margin-left: auto;
    margin-right: auto;
padding:20px;
}
</style>
</head>
<body style='height:400px;background-color:black'>
<div class='max content_wrapper'>
<?php if($uid>0){ ?>
  <a href='picrewards://' class=btn>Back To PhotoRewards</a>
<?php }else {?>
  <a class=btn href='https://itunes.apple.com/app/id662632957?mt=8'>Get PhotoRewards Now</a>
<?php }?>
<br><center><b><?php echo "<a href='$clicklink'>$title</a>" ?></b></center>
<center><?php echo "<a href='$clicklink'>" ?><img height=180px align=middle src='http://json999.com/pr/uploads/<?php echo $dir.$pid?>.jpeg'></a></center>
<?php if($uid>0){
 echo "<b>Share this app with your friends!<br>Earn $extra points for each download!";
 echo "<p>Direct Link: <br><a href=$clicklink>$clicklink</a></p>";
 echo "<p><a href=$publish>Share on Facebook</a>";
 echo "<p><a href=$tweet>Tweet</a>";
// echo "<p><a href='sms:?body=".urlencode($tweetmsg." ".$clicklink)."'>Send SMS</a>";
}else{
 echo "<p>$title - Free App<br><a href=$clicklink><img width=150 src=http://www.castlen.com/elements/App-Store-Badge.png></a></p>";
}?>
<div style='clear:both;position:relative;bottom:2px'>
<table width=100%><tr>
<td><a class='btn' href='/pr/p22.php?uid=<?php echo $uid ?>&cmd=prev&h=<?php echo $h ?>'><- Prev </a></td>
<td><a class='btn' href='/pr/p22.php?uid=<?php echo $uid ?>&cmd=next&h=<?php echo $h ?>'>Next -> </a></td>
</tr></table>
</div>
</div>
<script>
var CSS = document.documentElement.style;
/mobile/i.test(navigator.userAgent) && !pageYOffset && !location.hash && setTimeout(function () {
  CSS.height = '200%';
  CSS.overflow = 'visible';
  window.scrollTo(0, 1);
  CSS.height = window.innerHeight + 'px';
  CSS.overflow = 'hidden';
},100);

<?php if($showtut==1) {?>
	alert("Share App Screenshots and earn points instantly! Share the 'direct link' to your friends and earn MORE points when they download the App!");
<?php } ?>

<?php if($e_msg!="") {?>
 var msg="<?php echo $e_msg ?>";
 alert(msg);
 //window.location="http://www.json999.com/pr/p22.php?h=$h&uid=<?= $uid ?>";
<?php } ?>
</script>
</html>
