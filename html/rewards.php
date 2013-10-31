<?php
require_once("/var/www/lib/functions.php");
$ua=$_SERVER['HTTP_USER_AGENT'];
$reviewer=0;

if(strpos($ua,"PictureRewards/1.3")!==false){
 $reviewer=1;
}
$latestversion=0;
if(strpos($ua,"PictureRewards/1.2")!==false){
 $latestversion=1;
}

$uid=$_GET['uid'];
$rows=db::rows("select * from rewards where available>0 order by display_order asc");
$rr=array();
foreach($rows as $r){
 if($reviewer==1 && ($r['id']==1 || $r['id']==4 || $r['id']==5)) continue;
 if($latestversion==0) $r['requiresEmail']=0;
 $r['postext']="Click OK to redeem ".$r['Points']." for a $".$r['CashValue']." ".$r['name']." Gift Card";
 if($r['requiresEmail']=="0") $r['postext']=$r['postext']."\n\rGift Card code will be available instantly and recorded under 'My Account' -> 'History'";
 if($r['requiresEmail']==1 && $r['Type']=="gc") $r['postext']=$r['postext']."\n\nGift Card code will be delivered via Email. Please enter your email address below:";
 if($r['requiresEmail']==1 && $r['Type']=="Paypal") $r['postext']=$r['postext']."\n\nPlease enter your PayPal email address below:";
 $rr[]=$r;
}
die(json_encode($rr));
