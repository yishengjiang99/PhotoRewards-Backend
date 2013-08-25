
<?php
require_once("/var/www/lib/functions.php");

$mac=$_GET['mac'];
$idfa=$_GET['idfa'];
$cb=$_GET['cb'];
$user=db::row("select * from appuser where app='$cb' and mac='$mac'");
error_log(json_encode($user));
$newuser=0;
if(!$user){
  $newuser=1;
  db::exec("insert into appuser set app='$cb', mac='$mac',created=now(),modified=now(),idfa='$idfa'");
  $user=db::row("select * from appuser where app='$cb' and mac='$mac'");  
}else{
 $uid=$user['id'];
 db::exec("update appuser set modified=now(), visit_count=visit_count+1 where id=$uid");
}

if($cb=='slide'){
 $url="http://d1.appredeem.com/redeem.php?mac_addr=".md5($mac)."&appid=648179171&ssk=373561131f926acea43d075e1a63735c";
// file_get_contents($url);
}

$gmtitle="";
$gm="";
$gmurl="";
$um="n";
$checkup="";

 if($mac=='84:29:99:27:F9:B2' || $mac='A0:ED:CD:75:37:88'){
  $gmtitle='hello';
  $gm="go to appdog";
  $gmurl="https://m.appdog.com/m/enroll.jsp?mac=$mac&idfa=$idfa";
 } 

if($user['xp']>0){
 $gmtitle="You won!";
 $gmtitle="Please leave some helpful feedback in the App Store";
 $gm="Thx!";
 $gmurl="https://userpub.itunes.apple.com/WebObjects/MZUserPublishing.woa/wa/addUserReview?id=648179171&type=Purple+Software";
}

$ret=array(
"gmtitle"=>$gmtitle,
"gm"=>$gm,
"gmurl"=>$gmurl,
"um"=>$um,
"mac"=>$mac,
"checkup"=>$checkup,
);

if($cb=="slide"){
 $ret['puppies']=get_puppies($mac,$idfa);
 $ret['unlocked']=unlocked_assets($mac);
}
if(true || $newuser==1 && ($cb=='stockalerts' || $cb=="slide")){
 $cbIds=array("stockalerts"=>642101022,"slide"=>648179171);
 $appid=$cbIds[$cb];
 $prcb="https://json999.com/sponsored_callback.php?mac=$mac&idfa=$idfa&storeID=$appid&pw=dafhfadsfkdsadlds";
echo $prcb;
 exec("curl '$prcb' > /dev/null 2>&1 &");
 error_log($prcb);
}

die(json_encode($ret));

function get_puppies($mac,$idfa){
  $pp= db::rows("select * from slide_assets where id !=10 order by display_order asc, id");
  foreach($pp as &$p){
   if($mac=='5C:95:AE:2F:3B:63' ||$mac=='B0:65:BD:7A:47:B5' || $mac=='9C:04:EB:3A:C7:88'){
  	 $p['locked']=0;	
    }
   if(true || $p['id']==2){
    $p['interstitial']="Free $0.50 Amazon Giftcard from Appdog.com. \n\nLogin with Facebook to earn 50 Appdog Bones which you can use to redeem for Amazon Giftcards";
    $p['actionurl']="https://m.appdog.com/m/enroll.jsp?cb=".$_GET['cb']."&idfa=".$idfa."&mac=".$_GET['mac']."&mfraid=9122847575669700870&mfrzid=9135311512939093920&country_code=us&mfssaid=";
   }
   $p['prompt']=str_replace("STAR_COST_HERE",$p['cost'],$p['prompt']);
   $p['actionurl']=str_replace("MAC_ADDRESS_HERE",$mac,$p['actionurl']);
  }
 return $pp;
}
function unlocked_assets($mac){
  return db::rows("select * from unlocked_assets where mac='$mac'");
}
