<?php
require_once("/var/www/lib/functions.php");
$idfa=$_GET['idfa'];
$mac=$_GET['mac'];
$user=db::row("select * from appuser where mac='$mac' and idfa='$idfa' and app='slots' limit 1");

$uid=$user['id'];

$apps="select b.id,completions, a.Name, a.IconURL, b.click_url as RedirectURL from offers b join apps a on a.id=b.storeID where completions>0 order by completions desc limit 3";
$offers=db::rows($apps);

$o=array();
foreach($offers as $offer){
 $subid=$uid.",".$offer['id'].",2";
 $offer['RedirectURL']=str_replace("SUBID_HERE",$subid,$offer['RedirectURL']);
 $offer['RedirectURL']=str_replace("IDFA_HERE",$idfa,$offer['RedirectURL']);
 $offer['RedirectURL']=str_replace("MAC_HERE",$mac,$offer['RedirectURL']);

 $offer['wintxt']="Click OK to download ".$offer['Name']." Now";
 $offer['subid']=$subid;
 $offer['tag']=$subid;
 $o[]=$offer;
}
shuffle($o);
die(json_encode($o));
