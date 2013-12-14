<?php
$t1=microtime(true);
require_once("/var/www/lib/functions.php");
$ip=getRealIP();
$mac=$_GET['mac'];
$idfa=$_GET['idfa'];
$cb=$_GET['cb'];
$ua=$_SERVER['HTTP_USER_AGENT'];
$reviewer=0;
if(strpos($ua,"PictureRewards/1.3")!==false){
 $reviewer=1;
}
$uid=intval($_GET['uid']);
if($uid){
 $user=db::row("select * from appuser where id=$uid");
}
else if($mac=='ios7device'){
  $user=db::row("select * from appuser where app='$cb' and idfa='$idfa' order by id limit 1");
  if($user && $user['idfa']!=$idfa){
    $uid=$user['id'];
     db::exec("update appuser set idfa='$idfa' where id=$uid");
  }
}else if($idfa=='notios6yet' || $idfa=='(null)'){
  $user=db::row("select * from appuser where app='$cb' and mac='$mac' order by id limit 1");
}else{
  $user=db::row("select * from appuser where app='$cb' and mac='$mac' and idfa='$idfa' order by id limit 1");
}

$newuser=0;
if(!$user){
  $newuser=1;
  db::exec("insert into appuser set ipaddress='$ip', app='$cb', mac='$mac',xp=1,created=now(),modified=now(),idfa='$idfa'");
  $uid=db::lastID();
  $user=db::row("select * from appuser where id=$uid");
}else{
 $uid=$user['id'];
 db::exec("update appuser set modified=now(), visit_count=visit_count+1 where id=$uid");
}

$config=array("um"=>"n","mac"=>$mac);
$config['xp']=$user['xp'];
$config['uid']=$user['id'];
$config['stars']=$user['stars'];
$config['fbid']=$user['fbid'];
if($cb=="stockalerts"){
	$ua=$_SERVER['HTTP_USER_AGENT'];
	if(strpos($ua,"stockalerts")!==false){
                $config['gm']="Login with Facebook to earn 50 Appdog Bones which you can use to redeem for Amazon.com Gift Cards, iTunes Giftcard or PayPal Cash.\n\nClick 'OK' to visit appdog.com.";
                $config['gmtitle']="FREE $0.50 Amazon Giftcard, PayPal Cash or iTunes Giftcard.";
                $config['gmtitle']="FREE Amazon Giftcard, PayPal Cash or iTunes Giftcard.";
 		$config['gm']="Upload Pictures\nEarn Points\nGet FREE GIFT CARDS\n";
                $config['gmurl']="https://m.appdog.com/m/enroll.jsp?cb=".$_GET['cb']."&idfa=".$idfa."&mac=".$_GET['mac']."&mfraid=9122847575669700870&mfrzid=9135311512939093920&country_code=us&mfssaid=$uid";
		$config['gmurl']="https://www.json999.com/redirect.php?from=stockalert";
	}
}
$cbIds=array("stockalerts"=>642101022,"slide"=>648179171,'projectile'=>644641252,'projectilefree'=>644782348,"contest"=>755182884);
if($newuser==1 && isset($cbIds[$cb])){
 $appid=$cbIds[$cb];
 $prcb="https://json999.com/sponsored_callback.php?mac=$mac&idfa=$idfa&storeID=$appid&pw=dafhfadsfkdsadlds";
 exec("curl '$prcb' > /dev/null 2>&1 &");
 error_log($prcb);
}

if($cb=="picrewardsdev" || $cb=="picrewards" || $cb=="contest"){
 require_once("/var/www/html/pr/levels.php");
 $nickname=$user['username'];
 if($nickname==''){
     $nickname='newuser';
  }
 $config['nickname']=$nickname;
 $config['fbcaption']="Upload Pictures; Earn Free Rewards";
 $xpinfo=getBonusPoints($user['xp'],$user['country']);
 $config=array_merge($config,$xpinfo);
 $maxbonus=$xpinfo['maxbonus'];
 $longmsg="Download #PhotoRewards from the AppStore and enter my bonus code '$nickname' for up to $maxbonus Bonus Points.\nPoints can be redeemed for PlayStation, Starbucks, iTunes, Hulu Plus, xBox LIVE giftcards or PayPal Cash!";
 $longmsg.=" http://photorewards.net/$nickname";
 $config['tweetmsg']="#PhotoRewards http://photorewards.net/$nickname";
 $config['fbmsg']=$longmsg;
 $config['emailmsg']=$longmsg;
 $config['redirect']="https://photorewards.net/$nickname";
 $bidconfig=array(
 "categories"=>explode(",", "#apps,#food,#cars,#People,#Music,#Games,#Movies,#youtube,#nature,#party,#swag,#yolo,#family,#Animals,#beautiful,#ideas,#sleep,#pillow,#fun"),
 "bidtiers"=>array(1,2,3,4,5),
// "myNumber"=>"tel://6508046836"
 );

 $deviceInfo=$user['deviceInfo'];
 if($deviceInfo=="" && $reviewer==0){
   $config['um']='y';
   $config['checkup']="https://www.json999.com/deviceInfo.php?uid=$uid&cb=$cb&idfa=$idfa";
 }
 $config['dir']="You must try this app and take a screenshot from within the app before you can upload a picture!\nClick OK to download from the App Store!";
 $config['cc']="Off-Topic,Spammer,Explicit Content, Kiks,Religious Intolerance,Racial intolerance,Excessive Profanity,Poor Quality";
 if($cb=="contest"){
   $username=$user['username'];
   $config['postJoin']="To share with friends and ask them for 5-star ratings, ask them to search for username '$username' in the search bar";
   $config['menu']="Free App|Leader Board|Recent Winners";
   $config['searchBarPlaceHolder']="Search for usernames or hashtags";
   $config['email']=$user['email'];
   $config['cmd']= "Command List:\n1. type 'change username [new name]' (currently $username).\n2. Type change email [your email address] to change email.".
 "\n3. Type 'login fb' to login with Facebook.\n4. Type 'history' to see history.\n5. Type 'friends' to see who gave you 5 stars";
  $config['cmd']= "Coming soon";
 } 
 $config=array_merge($config,$bidconfig);
}
die(json_encode($config));
