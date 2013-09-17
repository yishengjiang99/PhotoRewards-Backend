<?
error_log(json_encode($_POST));
require_once('/var/www/lib/functions.php');
$r=$_POST;
$uid=intval($r['uid']);
$pid=stripslashes($r['picid']);
$cc=$r['complaint'];
if($uid==2902 && $cc=='Racial intolerance'){
 db::exec("update UploadPictures set reviewed=1 where id='$pid'");
 error_log("update UploadPictures set reviewed=1 where id='$pid'");
}else{
 $user=db::row("select * from appuser where id=$uid");
 $role=$user['role'];
 $isadmin=0;
 $msg="Your report has been set to the Information Safety Committee";
  $username=$user['username'];
 if($username=='superadmin') $username='redcat';
 error_log("update UploadPictures set reviewed=-1 where id='$pid'");
 db::exec("update UploadPictures set reviewed=-1 where id='$pid'");
 $pic=db::row("select * from UploadPictures where id='$pid'");
error_log(json_encode($pic));
 $upuid=$pic['uid'];
 if($role>0){
   $isadmin=1;
   error_log("$uid is admin");
   db::exec("update appuser set banned=5 where id=$upuid limit 1");
 error_log("update appuser set banned=5 where id=$upuid limit 1"); 
  $msg="User has been banned";
 }
 require_once("/var/www/html/pr/apns.php");
// apnsUser($upuid,"$username reported that your picture: $cc","$username reported that your picture: $cc","http://www.json999.com/pr/picture.php?id=$pid");
 apnsUser(2902,"$username reported that your picture: $cc","$username reported that your picture: $cc","http://www.json999.com/pr/picture.php?id=$pid");

}
 $e="report_picture";
 if(time() % 1==0) db::exec("insert into app_event set t=now(), name='$e', m=1");

die(json_encode(array("title"=>"Thanks","msg"=>$msg)));
