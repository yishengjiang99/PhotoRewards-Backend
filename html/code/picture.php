<?php
$id=$_GET['id'];
$uid=intval($_GET['uid']);
if(intval($id)!=0 && $_GET['r']=='r'){
 require_once("/var/www/lib/functions.php");
 $uid=$id;
 $mid=$_GET['mid'];
 $t=$_GET['t'];
 $h=$_GET['h'];
 $cb=$_GET['cb'];
 if($h!=md5($uid.$mid."watup".$t)) die("rong");
 $select ="select aes_decrypt(code,'supersimple') as Reward, concat('',b.name) as Item from reward_codes a join rewards b on a.reward_id=b.id where a.rewarded_to_uid=$uid";
 $rewards=db::rows($select);
}else{
 $url="http://www.json999.com/pr/uploads/$id.jpeg";
 header("location: http:/www.json999.com/pr/p.php?uid=$uid&imgurl=$url");
 exit;
}
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
<head>
<style type="text/css">
a, a:visited { color:#0645AD;font-size:151% }
BLOCKQUOTE { margin-right:3px;margin-left:15px; }
ul { margin-left:0px; padding-left:0px; list-style-type:none; }
</style>
</head>
<body>

<div id="punwrap">
<h2>Redemption History <a href='<?php echo $cb?>://'>Back To the App</a></h2>
<br><br><br>
			<?php 
foreach ($rewards as $r){
echo "<li>";
echo $r['Item'].": ".$r['Reward'];
}
?>
</body>
</html>
