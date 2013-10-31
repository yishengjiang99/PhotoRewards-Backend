<html>
<form method=POST>
uid: <input type=text name=uid />
username: <input type=text name=username />
email: <input type=text name=email />
<input type=submit />
</form>
<?php

var_dump($_POST);
if(isset($_POST) || isset($_GET)){
 require_once("/var/www/lib/functions.php");
 $username=$_POST['username'];
 $email=$_POST['email'];
 $uid="";
 if($_POST['uid']) $uid=$_POST['uid'];
 if($_GET['uid']) $uid=$_GET['uid'];
 if($username!=''){
  $user=db::row("select concat('/admin/unban.php?uid=',c.id) as linkunban, id, username,mac,idfa,ipAddress,country,created,modified,ltv,stars,email from appuser c where app='picrewards' and username='$username'");
 }
 if($email!=''){
   $user=db::row("select concat('/admin/unban.php?uid=',c.id) as linkunban, id, username,mac,idfa,ipAddress,country,created,modified,ltv,stars,email from appuser c where app='picrewards' and email like '$email%'");
 }
 if($uid!=""){
   $user=db::row("select concat('/admin/unban.php?uid=',c.id) as linkunban, id, username,mac,idfa,ipAddress,country,created,modified,ltv,stars,email from appuser c where c.app='picrewards' and c.id=$uid");
echo "select concat('/admin/unban.php?uid=',c.id) as linkunban, id, username,mac,idfa,ipAddress,country,created,modified,ltv,stars,email from appuser c where c.app='picrewards' and c.id=$uid";
 }
 if($user){
  $ip=implode(".",array_slice(explode(".",$user['ipAddress']),0,3));
  $idfa=$user['idfa'];
  $mac=$user['mac'];
  $email=$user['email'];
if($email!=""){
    echo rows2table(db::rows("select concat('/admin/unban.php?uid=',c.id) as linkunban, id, username,mac,idfa,ipAddress,country,created,modified,ltv,stars,email,banned from appuser c where app='picrewards' and email='$email'"));
}
   echo	rows2table(db::rows("select concat('/admin/unban.php?uid=',c.id) as linkunban, id, username,mac,idfa,ipAddress,country,created,modified,ltv,stars,email,banned from appuser c where app='picrewards' and ipaddress like '$ip%'"));
   echo	rows2table(db::rows("select concat('/admin/unban.php?uid=',c.id) as linkunban, id, username,mac,idfa,ipAddress,country,created,modified,ltv,stars,email,banned from appuser c where app='picrewards' and mac!='ios7device' and mac='$mac'"));
   echo	rows2table(db::rows("select concat('/admin/unban.php?uid=',c.id) as linkunban, id, username,mac,idfa,ipAddress,country,created,modified,ltv,stars,email,banned from appuser c where app='picrewards' and idfa='$idfa'"));

 }
} 

