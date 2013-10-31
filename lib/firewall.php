<?php
require_once('/var/www/lib/functions.php');

$r=$_REQUEST;
$idfa=$r['idfa'];
$t=$r['t'];
$uid=$r['uid'];

$user=db::row("select * from appuser where id=$uid and idfa='$idfa'");
if(!$user && $idfa!='notios6yet') {
 error_log("firewall: fake user ".$_SERVER['REQUEST_URI'].json_encode($_REQUEST));
 die();
}
$h=$r['h'];
if(abs(intval($t)-time())>1000){
error_log("firewall: suspicious $t at".time()." ".$_SERVER['REQUEST_URI'].json_encode($_REQUEST));
//die();
}
if($h!=md5($t.$idfa."what1sdns?")){
 error_log("$h not matching ".md5($t.$idfa."what1sdns?"));
 die();
}
