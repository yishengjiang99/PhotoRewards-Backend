<?php
require_once("/var/www/lib/functions.php");
$uid=intval($_GET['uid']);
$user=db::row("select idfa,fbliked,fbid from appuser where id=$uid");
$idfa=$user['idfa'];
$code=$_GET['code'];

if(!$code || ($user['fbid']!=0 && $user['fbfriends']!=0 && $user['locale']!="")){
   header("location: picrewards://");
}

$cb="https://www.json999.com/pr/fblogin.php?uid=$uid";
$gettoken="https://graph.facebook.com/oauth/access_token?client_id=146678772188121&redirect_uri=".urlencode($cb)."&client_secret=de49dfd8e172bfb840036a53e44c5d7c&code=$code";
$ret=file_get_contents($gettoken);

preg_match("/access_token=(.*?)&expires=/",$ret,$m);
$token="";
if(isset($m[1])){
 $token=$m[1];
}
$pointsearned=0;
if($token!=""){
 $friendCnt=-1;
 $url="https://graph.facebook.com/me/friends?access_token=$token";
 $fbfriends=json_decode(file_get_contents($url),1);
 if($fbfriends && $fbfriends['data']) $friendCnt=count($fbfriends['data']);
 $url="https://graph.facebook.com/me/?access_token=$token";
 $fbdata=file_get_contents($url);
 $json=json_decode($fbdata,1);
 $fbid=$json['id'];
 $email=$json['email'];
 $gender=$json['gender'];
 $fname=$json['first_name'];
 $locale='';
 if($json['locale']) $locale=$json['locale'];
 $merged=0;
 $rows=db::rows("select * from appuser where fbid=$fbid order by modified");
 if(count($rows)==0){
     $pointsearned=20;
     if($user['fbid']==0) db::exec("update appuser set stars=stars+$pointsearned where id=$uid limit 1");
  }else if(count($rows)>5){
	$recent=db::row("select count(1) as cnt from appuser where fbid=$fbid and created>date_sub(now(), interval 3 day)");
	$recent=$recent['cnt'];	
	if($recent>4){	
          $note=count($rows)." devices with same fbaccount";
          error_log("update appuser set banned=1, note='$note' where id=$uid");
          db::exec("update appuser set banned=1, note='$note' where id=$uid");
	}
  }else if(count($rows)>=1){
 	$pstar=0;$pxp=0;$pltv=$row['ltv'];$puid=0;$username="";
	foreach($rows as $row){
		if($row['active']==0) continue;
		if($row['id']==$uid) continue;
		if($row['banned']==1){	
			$note="same fbid $fbid as banned user ".$row['id'];
			//db::exec("update appuser set banned=1,note='$note' where id=$uid"); break;
		}
		$merged=1;
  		error_log("dup user merging from ".$row['id']." to ".$uid);
		$pstar=$row['stars'];
	        $pxp=$row['xp'];
 		$username=$row['username'];
  		$puid=$row['id']; 
		$pltv=$row['ltv'];
	}
	if($puid!=0){
		db::exec("update appuser set stars=stars+$pstar,username='$username', xp=xp+$pxp,has_entered_bonus=1,ltv=$pltv,active=1 where id=$uid");
		db::exec("update appuser set username='', stars=0,xp=0,active=0,ltv=0 where id=$puid");
	}
 }
 db::exec("insert ignore into fbusers set fbid=$fbid,email='$email',gender='$gender',mac='$mac',firstname='$fname', uid=$uid, fbdata='$fbdata', friend_count=$friendCnt on duplicate key update friend_count=$friendCnt");
 db::exec("update appuser set fbid=$fbid,fbfriends=$friendCnt,locale='$locale' where id=$uid limit 1");
 if($user['email']==""){
    db::exec("update appuser set email='$email' where id=$uid limit 1");
 }
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
<?php if ($merged>0){ ?>
alert("Logged In");
<?php }?>
window.location="picrewards://";
</script>
</body>
</html>
