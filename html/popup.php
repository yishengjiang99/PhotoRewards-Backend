<?php
require_once("/var/www/lib/functions.php");

$mac=$_GET['mac'];
$idfa=$_GET['idfa'];
$cb=$_GET['cb'];
$user=db::row("select * from appuser where app='$cb' and mac='$mac'");
$uid=$user['id'];
$url="http://ringpartner.ringrevenue.com/c/398/21273-116449-8044908?us=%2Faff_c%3Foffer_id%3D106%26aff_id%3D2200%26aff_sub%3D8886573708%26PPCPN%3D8886573708";
$url="tel:+18886573708";
$title="Call Now for Better Home Security";
$message="Free To Call";
die(json_encode(array("title"=>$title,"actionurl"=>$url,"message"=>$message,"k"=>"s")));
