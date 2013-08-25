<?php
function email($to,$subject,$body,$from="support@json999.com"){
        $host="smtp.gmail.com";
        $headers = "From: $from\r\n" .
        'X-Mailer: PHP/' . phpversion() . "\r\n" .
        "MIME-Version: 1.0\r\n" .
        "Content-Type: text/html; charset=utf-8\r\n" .
        "Content-Transfer-Encoding: 8bit\r\n\r\n";
        ini_set("SMTP",$host);
        ini_set("smtp_port","25");
        ini_set("sendmail_from","$from");
        return mail($to, $subject, $body, $headers);
}

$msg="";
if(isset($_POST['submit_button'])){
 if(!isset($_POST['email']) || !isset($_POST['first_name']) || !isset($_POST['last_name'])){
	$msg="Email, first and last names are required fields";	
 }elseif(!isset($_POST['comments'])){
	$msg="Please enter an comment";
  }else{
   $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);  
   $from =$_POST['first_name']." ".$_POST['last_name']."<$email>";
   $to="yisheng.jiang@gmail.com";
   $comment=$_POST['comments'];
   email($to,"Json999.com support email",$comment,$from);
  $msg="<div class=success>Thank you for your feedback. Your info has been submitted to our team.</div>";
 }
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>GrepAwk LLC</title>

<link rel="stylesheet" href="/mobform.css" type="text/css" media="screen, projection"/>
<link rel="stylesheet" href="/screen.css" type="text/css" media="screen, projection"/>

<link href="callcentre.css" rel="stylesheet" type="text/css" />
<style type="text/css">
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
	background-image: url(images/bg.jpg);
	background-repeat: repeat-x;
}
.footer {
    bottom: 0;
    clear: both;
    top:-61px;
	position:relative;
    text-align: center;
    width: 870px;
}
.nav {
    float: left;
    font-size: 125%;
  left: -7px;
    position: relative;
    top: 14px;
}

.nav ul {
    margin: 0;
}
ul {
    list-style: disc outside none;
    margin-bottom: 1em;
}
.nav li {
    background: none repeat scroll 0 0 #777777;
    display: block;
    float: left;
}
.nav a {
    color: #FFFFFF;
    display: block;
    padding: 6px 12px;
    text-decoration: none;
}
#apDiv1 {
	position:absolute;
	width:924px;
	height:352px;
	z-index:1;
	left: 33px;
}
#apDiv2 {
	position:absolute;
	width:168px;
	height:25px;
	z-index:1;
	left: 678px;
	top: 71px;
}
#apDiv3 {
	position:absolute;
	width:72px;
	height:28px;
	z-index:1;
	left: 854px;
	top: 69px;
}
#apDiv4 {
	position:relative;
	width:807px;
	height:auto;
	z-index:1;
	left: 76px;
	top:54px;
}
#apDiv5 {
	width:648px;
	height:20px;
	z-index:1;
}
</style>
</head>

<body>

<div id="wrapper">
  <table width="980" border="0" cellpadding="0" cellspacing="0">
    <tr>
    </tr>
    <tr>
    </tr>
    <tr>
      <td valign=top>
<div class="container">
    <div style="margin-top:25px;" class="span-17 last">
        <h1>Contact Us</h1>
		<div id="client_side_message_box" class="span-17 last">
<?php echo $msg ?>
		</div>
		<div id="client_side_message_box" class="span-17 last">
	        <h2 class="blue">If you want more info, please drop us a note.</h2>
	        <div id="contactFormContainer">
	            <form  method="post" name="contactForm" id="contactForm">
	                <div class="span-17 last">
	                    <div class="span-7">
	                        <label for="first_name">First Name*</label>
	                        <input type="text" maxlength="50" value="" name="first_name" class="text">
	                    </div>
	                    <div class="span-10 last">
	                        <label for="last_name">Last Name*</label>
	                        <input type="text" maxlength="50" value="" name="last_name" class="text">
	                    </div>
	                    <div class="span-7">
	                        <label for="email">Email*</label>
	                        <input type="text" maxlength="50" value="" name="email" class="text">
	                    </div>
	                    <div class="span-10 last">
	                        <label for="company_name">Company Name</label>
	                        <input type="text" maxlength="50" value="" name="company_name" class="text">
	                    </div>
	                    <div class="span-17 last">
	                        <label for="comments">Comments or Questions</label>
	                        <textarea style=height:200px name="comments"></textarea>
	                    </div>
				                            <div class="span-17 last">

                                        <button value="0" class="submit" type="submit" id="submit_button" name="submit_button">submit</button>
			      </div>
	                </div>

	            </form>
	        </div>
        </div>
    </div>
</div>
</td>
    </tr>
    <tr><td>
  </td>  </tr>
  </table>
</div>
</body>
</html>
