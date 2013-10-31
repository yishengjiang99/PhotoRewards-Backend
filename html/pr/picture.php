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
 $url="http://www.json999.com/pr/p22.php?pid=".$id;
 header("location: $url");
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
<br><br><br>
*Amazon.com is not a sponsor of this promotion. Except as required by law, Amazon.com Gift Cards ("GCs") cannot be transferred for value or redeemed for cash. GCs may be used only for purchases of eligible goods on
Amazon.com or certain of its affiliated websites.  Purchases are deducted from the GC balance. To redeem or view a GC balance, visit "Your Account" on Amazon.com. Amazon is not responsible if a GC is lost, stolen,
destroyed or used without permission. For complete terms and conditions, see <a href="http://www.amazon.com/gc-legal">www.amazon.com/gc-legal</a>. GCs are issued by ACI Gift Cards, Inc., a Washington corporation. All Amazon
&trade; &amp; &copy; are IP of Amazon.com or its affiliates. No expiration date or service fees.
</body>
</html>
