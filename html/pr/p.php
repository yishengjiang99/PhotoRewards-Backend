<?php
require_once("/var/www/lib/functions.php");
$r=$_REQUEST;
$uid=intval($r['uid']);
$pid=$r['pid'];
$pid=str_replace("t/","",$pid);
$pid=str_replace("m/","",$pid);
$info=db::row("select compressed,a.uid as uploader, b.uid as offerer, a.points_earned from UploadPictures a join PictureRequest b on a.refId=b.id where a.id='$pid';");
error_log("select a.uid as uploader, b.uid as offerer, a.points_earned from UploadPictures a join PictureRequest b on a.refId=b.id where a.id='$pid'");
       if($info['compressed']==5){
         $dir="arch/";
       }
$uploader=$info['uploader'];
$offerer=$info['offerer'];

if($uid==2902){
//var_dump($info);
}
$points=$info['points_earned'];
$xp=ceil($points*13);
$h="";
if(false && $uid==$offerer){
 $h=md5($uid."dddd");
 $confirm="onsubmit=\"return confirm('Do you want to demand a refund for $points Points. You will lose $xp XP')\"";
}else{
 $confirm="";
}
$reportedandreturn=0;
if(isset($r['report'])){
   $reportedandreturn=1;
 error_log("update UploadPictures set reviewed=reviewed-1 where id='$pid'");
 $user=db::row("select * from appuser where id=$uid");
 $username=$user['username'];
 if($username=='superadmin') $username='redcat';
 $isadmin=0;
 if($user['role']>0){
   $isadmin=1;
   error_log("$uid is admin p.php");
 }
 db::exec("update UploadPictures set reviewed=-1 where id='$pid'");
 $pic=db::row("select * from UploadPicture where id='$pid'");
 $upuid=$pic['uid'];
 require_once("/var/www/html/pr/apns.php");
 $cc=$r['complaint'];
 apnsUser($upuid,"$username reported that your picture: $cc","$username reported that your picture: $cc","http://www.json999.com/pr/picture.php?id=$pid");
 apnsUser(2902,"$username reported that your picture is: $cc","$username reported that your picture: $cc","http://www.json999.com/pr/p.php?pid=$pid&uid=$upuid");
 if($isadmin){
   db::exec("update appuser set role=-1 where id=$upuid");
   error_log("appuser $upuid banned by $uid");
 }
 if(false && isset($r['h']) && $r['h']!='' && $r['h']=$h && $uid=$offerer){
//   db::exec("update appuser set stars=stars-$points where id=$uploader");
//   db::exec("update appuser set	stars=stars+$points where id=$offerer");
//    db::exec("update appuser set xp=xp-$xp where id=$offerer");
   $reportedandreturn=1;
 }
}
?>

<html>
<head>
<meta name = "viewport" content = "user-scalable=no, initial-scale=1.0, maximum-scale=1.0, width=device-width">
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
</head>
 <a href='picrewards://' class=btn><h1>Back To PhotoRewards</h1></a>
<br>
<img width=100% src='http://json999.com/pr/uploads/<?= $dir.$pid?>.jpeg'>
<script>
<?php if($reportedandreturn==1){
echo ' alert("Thanks for reporting this picture to the committee!");';
echo 'window.location="picrewards://"';
}?>
</script>
</body>
</html>
