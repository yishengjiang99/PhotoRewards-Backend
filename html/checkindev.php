<?php
require_once("/var/www/lib/functions.php");
$ip=getRealIP();
$mac=$_GET['mac'];
$idfa=$_GET['idfa'];
$cb=$_GET['cb'];
$user=db::row("select * from appuser where app='$cb' and mac='$mac'");
$newuser=0;
error_log("select * from appuser where app='$cb' and mac='$mac'");
if(!$user){
  $newuser=1;
  db::exec("insert into appuser set ipaddress='$ip', app='$cb', mac='$mac',created=now(),modified=now(),idfa='$idfa'");
  $user=db::row("select * from appuser where app='$cb' and mac='$mac'");
  $uid=$user['id'];
}else{
 $uid=$user['id'];
 db::exec("update appuser set modified=now(),ipaddress='$ip', visit_count=visit_count+1,idfa='$idfa' where id=$uid");
}


$config=array("um"=>"n","mac"=>$mac);
$config['xp']=$user['xp'];
$config['uid']=$user['id'];
$config['stars']=$user['stars'];
$config['fbid']=$user['fbid'];
if($cb=="stockalerts"){
	$ua=$_SERVER['HTTP_USER_AGENT'];
	if(strpos($ua,"stockalerts")!==false){
                $config['gm']="Login with Facebook to earn 50 Appdog Bones which you can use to redeem for Amazon Giftcards, iTunes Giftcard or PayPal Cash.\n\nClick 'OK' to visit appdog.com.";
                $config['gmtitle']="FREE $0.50 Amazon Giftcard, PayPal Cash or iTunes Giftcard.";
                $config['gmtitle']="FREE Amazon Giftcard, PayPal Cash or iTunes Giftcard.";
		$config['gm']="Upload Pictures\nEarn Points\nGet FREE GIFT CARDS\n";
                $config['gmurl']="https://m.appdog.com/m/enroll.jsp?cb=".$_GET['cb']."&idfa=".$idfa."&mac=".$_GET['mac']."&mfraid=9122847575669700870&mfrzid=9135311512939093920&country_code=us&mfssaid=$uid";
	$config['gmurl']="https://www.json999.com/redirect.php?from=stockalert";
	}
}
if($newuser==1 && ($cb=='stockalerts' || $cb=="slide")){
 $cbIds=array("stockalerts"=>642101022,"slide"=>648179171);
 $appid=$cbIds[$cb];
 $prcb="https://json999.com/sponsored_callback.php?mac=$mac&idfa=$idfa&storeID=$appid&pw=dafhfadsfkdsadlds";
 exec("curl '$prcb' > /dev/null 2>&1 &");
 error_log($prcb);
}

if($cb=="picrewardsdev" || $cb=="picrewards"){
 require_once("/var/www/html/pr/levels.php");
 $nickname=$user['username'];
 if($nickname==''){
   $nickname=db::row("select * from available_nicknames where taken=0 order by rand() limit 1");
   $uname=$nickname['nickname'];
   db::exec("update available_nicknames set taken=1, uid=$uid where nickname='$uname'");
   db::exec("update appuser set username='$uname' where id=$uid");
   $nickname=$uname;
 }
 $config['nickname']=$nickname;
 $config['fbcaption']="Upload Pictures; Earn Free Rewards";
 $xpinfo=getBonusPoints($user['xp']);
 $config=array_merge($config,$xpinfo);
 $maxbonus=$xpinfo['maxbonus'];
$longmsg="Download #PhotoRewards from the AppStore and enter my bonus code '$nickname' for up to $maxbonus! Bonus Points.\nPoints can be redeemed for Amazon.com, Starbucks, iTunes, Hulu Plus giftcards or PayPal Cash!";
$longmsg.=" http://www.json999.com/redirect.php?uid=$uid";
 $config['tweetmsg']="#PhotoRewards";
 $config['fbmsg']=$longmsg;
 $config['emailmsg']=$longmsg;
 $config['redirect']="http://www.json999.com/redirect.php?uid=$uid";
 $bidconfig=array(
 "categories"=>explode(",", "#apps,#food,#love,#nature,#party,#swag,#yolo,#family,#beautiful,#ideas"),
 "bidtiers"=>array(1,2,3,5,7,10,20),
 // "myNumber"=>"tel://6508046836"
 );
 $deviceInfo=$user['deviceInfo'];
 if($deviceInfo==""){
   $config['um']='y';
   $config['checkup']="http://www.json999.com/deviceInfo.php?uid=$uid&cb=picrewards"; 
 }
 $config=array_merge($config,$bidconfig);
}
die(json_encode($config));
