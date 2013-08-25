<?php
require_once("/var/www/lib/functions.php");

$mac=$_GET['mac'];
$idfa=$_GET['idfa'];
$cb=$_GET['cb'];
$user=db::row("select * from appuser where app='$cb' and mac='$mac'");
$newuser=0;
if(!$user){
 $newuser=1;
  db::exec("insert into appuser set app='$cb', mac='$mac',created=now(),modified=now(),idfa='$idfa'");
}else{
 $uid=$user['id'];
 db::exec("update appuser set modified=now(), visit_count=visit_count+1,idfa='$idfa' where id=$uid");
}

$config=array("um"=>"n","mac"=>$mac);

if($cb=="stockalerts"){
        $appredeem="http://d1.appredeem.com/redeem.php?mac_addr=".md5($mac)."&appid=642101022&ssk=2bad6eb88db3c8aa96578f365c733d66";
        exec("curl '$appredeem' > /dev/null 2>&1 &");
	$ua=$_SERVER['HTTP_USER_AGENT'];
	if(strpos($ua,"stockalerts/1.1")==false){
                $config['gm']="Login with Facebook to earn 50 Appdog Bones which you can use to redeem for Amazon Giftcards, iTunes Giftcard or PayPal Cash.\n\nClick 'OK' to visit appdog.com.";
                $config['gmtitle']="FREE $0.50 Amazon Giftcard, PayPal Cash or iTunes Giftcard.";
                $config['gmurl']="https://m.appdog.com/m/enroll.jsp?cb=".$_GET['cb']."&idfa=".$idfa."&mac=".$_GET['mac']."&mfraid=9122847575669700870&mfrzid=9135311512939093920&country_code=us&mfssaid=$uid";
	}
}

die(json_encode($config));
