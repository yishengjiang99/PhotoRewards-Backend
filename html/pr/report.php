<?
error_log(json_encode($_POST));
require_once('/var/www/lib/functions.php');
// {"picid":"6259d23faa5722f3a96bf94bb8d0e007","complaint":"Racial intolerance","idfa":"76983F58-EE80-47F9-BCE9-F83B674F324C","mac":"C0:63:94:43:00:08","cb":"picrewards","t":"1377371191","h":"ef75fd0448d41298bc8e5e0cc9e67d44","uid":"10597"}
$r=$_POST;
$uid=intval($r['uid']);
$pid=stripslashes($r['picid']);
$cc=$r['complaint'];

error_log("update UploadPictures set reviewed=-1 where id='$pid'");
db::exec("update UploadPictures set reviewed=-1 where id='$pid'");

die(json_encode(array("title"=>"Thanks","msg"=>"Your report has been set to the Information Safety Committee")));
