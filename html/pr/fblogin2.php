<?php
require_once("/var/www/lib/functions.php");
$uid=intval($_GET['uid']);
$user=db::row("select idfa,fbliked from appuser where id=$uid");
$idfa=$user['idfa'];
$code=$_GET['code'];
if(!$code){
  header("location: picrewards://");
}
$cb="https://www.json999.com/pr/fblogin2.php?uid=$uid&idfa=$idfa";
$gettoken="https://graph.facebook.com/oauth/access_token?client_id=146678772188121&redirect_uri=".urlencode($cb)."&client_secret=de49dfd8e172bfb840036a53e44c5d7c&code=$code";
$ret=file_get_contents($gettoken);
preg_match("/access_token=(.*?)&expires=/",$ret,$m);
$token="";
if(isset($m[1])){
 $token=$m[1];
}

$pointsearned=0;
if($token!=""){
 $url="https://graph.facebook.com/me/?access_token=$token";
 $fbdata=file_get_contents($url);
 $json=json_decode($fbdata,1);
 $fbid=$json['id'];
 $email=$json['email'];
 $gender=$json['gender'];
 $fname=$json['first_name'];
 $row=db::row("select * from appuser where fbid=$fbid");
 if(!$row){
     $pointsearned=20;
     db::exec("update appuser set stars=stars+$pointsearned where id=$uid limit 1");
 }
 db::exec("insert ignore into fbusers set fbid=$fbid,email='$email',gender='$gender',mac='$mac',firstname='$fname', uid=$uid,fbdata='$fbdata'");
 db::exec("update appuser set fbid=$fbid, email='$email' where id=$uid limit 1");
}
?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
     <meta content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport">
    <meta charset="utf-8">
    <title>PhotoRewards On Facebook</title>
    </head>
    <body style='max-width:300px;margin-left:auto;margin-right:auto;'>
 <script>
<?php if ($pointsearned>0){ ?>
alert("You earned 20 points!");
<?php }?>
window.location="picrewards://";
</script>
</body>
</html>
