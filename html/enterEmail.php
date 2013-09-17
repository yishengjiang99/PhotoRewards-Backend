<?php
require_once("/var/www/lib/functions.php");
//enterEmail.php?uid=3353&h=b0260d2dc97477bfc8ee7d8460fce8aa&t=1377506361&idfa=973DCEC1-AC2D-4A6C-8834-77762DC6E191&mac=10:DD:B1:BD:EB:F3
require_once("/var/www/lib/firewall.php");
$uid=$_GET['uid'];
$user=db::row("select stars,id,username,email from appuser where id=$uid");
$username=$user['username'];
$stars=$user['stars'];
$email=$user['email'];
$points=$stars;
if($stars>=9500) $points=$stars+500;
$dollar=number_format($points/1000,2);
?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
     <meta content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport">
    <meta charset="utf-8">
    <title>PhotoRewards On Facebook</title>
        <script src="http://code.jquery.com/jquery-1.8.3.js"></script>
    <link href="/css/bootstrap.css" rel="stylesheet">
    </head>
    <body style='max-width:300px;margin-left:auto;margin-right:auto;'>
 <a href='picrewards://' class=btn><h3>Back To PhotoRewards</h3></a>
<br><br>
<h2>Enter your PayPal Email</h2>
<p>Hello <?php echo $username ?>!</p>
<p>Please enter your email address to redeem <?= $stars ?> Points for $<?=$dollar ?> in PayPal Cash</p>
<br><input type='text' id=email value='<?= $email ?>' size=20 />
<br><input id=go type='submit' value='cash out' />
<script>
 $(document).ready(function(){
   $("#go").click(function(){
      var email=$("#email").val();
      if(email==''){
		alert("Please enter a valid email address");
		return false;
	}
      var data=$.parseJSON('<? echo json_encode($_REQUEST);?>');
      data.email=email;
      data.giftID=1;
      $.post("/redeem.php",data,function(ret){
		ret=$.parseJSON(ret);
		var title=ret.title; var msg=ret.msg;
		var gourl=ret.url;
		if(gourl){
			var r = confirm(title+'\n\n'+msg);
			if(r==true){
				window.location=gourl;
			}else{
				window.location="picrewards://";
			}
		}else{
			alert(title+'\n\n'+msg);
			window.location='picrewards://';
		}
	});
   });
 });
</script>
</body>
</html>
