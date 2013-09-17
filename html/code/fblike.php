<?php
require_once("/var/www/lib/functions.php");
$uid=intval($_GET['uid']);
$user=db::row("select idfa,fbliked from appuser where id=$uid");
$idfa=$user['idfa'];
$h=$_GET['h'];
if($h!=md5($uid.$idfa."fblikeh")){
  die("...");
}
$cb="uid=$uid&h=$h&p=".md5($uid."joker".$h);

if(isset($_GET['p'])){
 $p=$_GET['p'];
 if($p!=md5($uid."joker".$h)) die("..");
 if($user['fbliked']==0){
   db::exec("update appuser set stars=stars+20,fbliked=1 where id=$uid limit 1");  
   require_once("/var/www/html/pr/apns.php");
   apnsUser($uid,"You win! 20 points added","You win! 20 points added");
   db::exec("insert into pr_xp set uid=$uid,xp=20,created=now(),event='fb_like_points'");
 }
 header("location: picrewards://");
 exit;
}
?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
     <meta content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport">
    <meta charset="utf-8">
    <title>PhotoRewards On Facebook</title>
    <link href="/css/bootstrap.css" rel="stylesheet">
    </head>
    <body style='max-width:300px;margin-left:auto;margin-right:auto;'>
 <a href='picrewards://' class=btn><h3>Back To PhotoRewards</h3></a>
<br><br>
<h2>Like us on Facebook</h2>
<li><b>Be the first to know when a new App goes live</b>
<li><b>Never miss a double-xp, double-point event again</b>
<li><b>CLICK 'LIKE' for 20 points</b>
<div id="fb-root"></div>
<div class="fb-like-box" data-href="https://www.facebook.com/photorewards" data-width="292" data-show-faces="true" data-header="false" data-stream="false" data-show-border="true"></div>
<script type="text/javascript">
    window.fbAsyncInit = function() {
        FB.init({appId: '146678772188121', status: true, cookie: true, xfbml: true});
        FB.Canvas.setSize({ width: 300, height: 500 });
        FB.Event.subscribe('edge.create',
            function(response) {
	         window.location = "https://www.json999.com/pr/fblike.php?<?= $cb ?>"; 
            }
        );
    };
    //Load the SDK asynchronously
    (function() {
        var e = document.createElement('script'); e.async = true;
            e.src = document.location.protocol +
              '//connect.facebook.net/en_US/all.js';
            document.getElementById('fb-root').appendChild(e);
    }());
</script>
</body>
</html>
