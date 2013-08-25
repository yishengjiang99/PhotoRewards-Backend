<?php
require_once("/var/www/lib/function.php");
$r=$_REQUEST;
$uid=$r['uid'];
$pid=$r['pid'];
$info=db::row("select a.uid as uploader, b.uid as offerer, a.points_earned from UploadPictures a join PictureRequest b on a.refId=b.id where a.id='09aadb68b043213f89ce83906a5a13d9'");
$uploader=$info['uploader'];
$offerer=$info['offerer'];
$points=$info['points_earned'];
var_dump($info);
$xp=$points*15;
if($uid==$offerer){
 $h=md5($uid."dddd");
 $confirm="confirm('Do you want to demand a refund for $points Points. You will lose $xp XP')";
}else{
 $confirm="";
}
?>

<html>
<head>
<meta name = "viewport" content = "user-scalable=no, initial-scale=1.0, maximum-scale=1.0, width=device-width">
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
</head>
 <a href='picrewards://' class=btn><h1>Back To PhotoRewards</h1></a>
<br><br>
<form method=POST><input type=hidden value='<?= $_REQUEST['pid'] ?>' name='pid' />
<input type=hidden name=report value=1 />
<input type=hidden name=uid value=<?= $uid ?> />
Report this picture!
<select name='complaint'>
<option value='none'>Select reason</option>
<option value='shitty'>Picture looks like shit</option>
<option value='spam'>Poster is spammer</option>
<option value='offtopic'>Off-topic</option>
<option value='offensive'>Offensive/explicit</option>
</select>
<input type=submit value='complain' />
</form>
<img width=100% src='http://json999.com/pr/uploads/<?= $_REQUEST['pid'] ?>.jpeg'>
<script>
function confirm_refund()
{
  if(confirm("Do you want to demand a refund? You will lose 150% of the XP you gained."))
  {
    return 0;
  }
  else
  {
    return 1;
  }
}
</script>
</html>
