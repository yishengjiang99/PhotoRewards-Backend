<?php
require_once("/var/www/lib/functions.php");
/*
$mac=$_GET['mac'];
$idfa=$_GET['idfa'];
$cb=$_GET['cb'];
$user=db::row("select * from appuser where app='$cb' and mac='$mac'");
$uid=$user['id'];
$url="http://ringpartner.ringrevenue.com/c/398/21273-116449-8044908?us=%2Faff_c%3Foffer_id%3D106%26aff_id%3D2200%26aff_sub%3D8886573708%26PPCPN%3D8886573708";
$url="tel:+18886573708";
$title="Call Now for Better Home Security";
$message="Free To Call";
*/
                $url="https://m.appdog.com/m/enroll.jsp?cb=".$_GET['cb']."&idfa=".$idfa."&mac=".$_GET['mac']."&mfraid=9122847575669700870&mfrzid=9135311512939093920&country_code=us&mfssaid=$uid";

die(json_encode(array("title"=>"FREE $0.50 Amazon Giftcard, PayPal Cash or iTunes Giftcard.",
"actionurl"=>$url,"message"=>"Login with Facebook to earn 50 Appdog Bones which you can use to redeem for Amazon.com Gift Cards, iTunes Giftcard or PayPal Cash.\n\nClick 'OK' to visit appdog.com.","k"=>"s")));
