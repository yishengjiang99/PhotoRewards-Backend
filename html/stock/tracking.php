<?php
require_once('/var/www/lib/functions.php');
$mac=$_GET['mac'];
$sym=$_GET['sym'];
$l=number_format($_GET['lower'],2);
$u=number_format($_GET['upper'],2);

$cb=$_GET['cb'];
$user=db::row("select * from appuser where mac='$mac'");
$uid=$user['id'];
$restore="\n\rIf you had purchased this subscription on another device, please log in from the Settings tab to restore your subscriptions";
$restore="";
$freq=intval($_GET['freq']);
if($freq<=5){
    $pro=db::row("select * from iap where user_id=$uid and iap_id='com.ragnus.stockalerts.instant'");
    if(!$pro){
	    die("false|0|buyinstant|Pro subsription required|Please upgrade to PRO subscription to receive stock alerts in under 5 minutes. $restore");
    }	
}
$trackingnow=$user['tracking'];
$maxtrack=2;
$tracker=db::row("select * from stock_tracking where user_id=$uid and symbol='$sym'");

$eligible=1;
if($tracker){
 $tid=$tracker['id'];
 db::exec("update stock_tracking set upper_bound='$u', lower_bound='$l',frequency='$freq',modified=now(),lb_notified=0,ub_notified=0,last_notified=null where id=$tid");
}else{
 if(intval($maxtrack)<=intval($trackingnow)){
	$eligible=0;
	$iaps=db::rows("select * from iap where user_id=$uid and expires>now()");
	foreach($iaps as $iap){
		$extra=intval($iap['extra']);
		if($extra>=$trackingnow){
			$eligible=1;
			break;
		}
	}
	if(!$eligible){
		die("false|$trackingnow|buyannual|You have reached the maximum trackings at your subscription level| Would you like buy a 12-month subscription and track stocks with Instant Alerts?".$restore);
	}
 }
 db::exec("insert into stock_tracking set user_id=$uid,symbol='$sym',upper_bound='$u',lower_bound='$l',frequency='$freq',created=now()");
 db::exec("update appuser set tracking=tracking+1 where id=$uid");
}

die("true|You have successfully registered to receive alerts for '$sym' at $l and $u");
