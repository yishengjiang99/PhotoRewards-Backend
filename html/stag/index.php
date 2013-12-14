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
 if(!isset($_POST['email']) || !isset($_POST['author'])){
        $msg="Email, first and last names are required fields";
 }elseif(!isset($_POST['text'])){
        $msg="Please enter an comment under message";
  }else{
   $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
   $from =$_POST['author']."<$email>";
   $to="support@photorewards.net";
   $comment=$_POST['text'];
   email($to,"PhotoRewards/grepawk.com support email",$comment,$from);
   $msg="<div class=success>Thank you for your feedback. Your feedback has been sent to the CEO and his team.</div>";
 }
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>GrepAwk LLC</title>
<meta name="keywords" content="iOS Apps" />
<meta name="description" content="Independent App Developer" />
<link href="templatemo_style.css" rel="stylesheet" type="text/css" />
    <meta http-equiv="Content-Language" content="en-us" />
	<meta name="author" content="Niall Doherty" />
    <script src="js/jquery-1.2.1.pack.js" type="text/javascript"></script>
    <script src="js/jquery-easing.1.2.pack.js" type="text/javascript"></script>
    <script src="js/jquery-easing-compatibility.1.2.pack.js" type="text/javascript"></script>
    <script src="js/coda-slider.1.1.1.pack.js" type="text/javascript"></script>
    <!-- Initialize each slider on the page. Each slider must have a unique id -->
  <style>        iframe {
            overflow: hidden;
            border: none;
        }</style>
    <script type="text/javascript">
    jQuery(window).bind("load", function() {
    jQuery("div#slider1").codaSlider()
    // jQuery("div#slider2").codaSlider()
    // etc, etc. Beware of cross-linking difficulties if using multiple sliders on one page.
    });
    </script>

</head>
<body>

<div id="templatemo_site_title_bar_wrapper">

	<div id="templatemo_site_title_bar">
    
    	<div id="site_title">
            <h1><a style='font-size:19px' href="http://www.grepawk.com" target="_parent">
               GrepAwk LLC
                <span>Makes Great Apps</span>
            </a></h1>
        </div>
	
        <div style='position:relative;left:100px;top:20px;'><iframe style='height:47px;margin-top: -10px; margin-left: -11px;' scrolling="no" src='https://www.json999.com/fb_iframe.php'></iframe></div>
</div> <!-- end of templatemo_site_title_bar -->

</div> <!-- end of templatemo_site_title_bar_wrapper -->


<div id="templatemo_content_wrapper">

  <div id="templatemo_content">
    
    <!-- start of slider -->

<div class="slider-wrap">
	<div id="slider1" class="csw">
		<div class="panelContainer">
		
			<div class="panel" title="PhotoReward">
				<div class="wrapper">
					<h2>PhotoRewards - Upload Pictures. Get Free Gift Cards</h2>
					<div class="image_wrapper fl_image">
			                    <img src="http://grepawk.s3.amazonaws.com/screen-4-1.png" width=160 alt="image"/>&nbsp;&nbsp;&nbsp;
	                                    <img src="http://grepawk.s3.amazonaws.com/screen-4-2.png" width=160 alt="image"/>
    					</div>
                    <p class="em_text">- Upload Pictures.<br>- Earn Points<br>- Redeem for Gift Cards<br><li><b>PayPal Cashouts starting at $0.25<li>Amazon.com Gift Cards*, Starbucks Gift Cards, xBox Gift Cards, iTunes Gift Cards
                </b><br><br>*Amazon.com is not a sponsor of this program. For complete gift card terms and conditions, see, www.amazon.com/gc-legal. All Amazon ®,  ™ & © are IP of Amazon.com or its affiliates. No expiration date or service fees.
	<br><a href='https://itunes.apple.com/app/id662632957?mt=8'>
        <img width=200 src="http://www.castlen.com/elements/App-Store-Badge.png"></a>
                </p>
		<br>
			</div>
			</div>
			<div class="panel" title="Finance">
				<div class="wrapper">
					<h2>Free Realtime Alerts for NASDAQ</h2>
                                        <div class="image_wrapper fl_image">
                                            <img width=160 src="http://a4.mzstatic.com/us/r1000/093/Purple2/v4/25/07/ef/2507efe6-4e11-8ff9-5159-dcb2bccd35e5/mzl.vodibsrb.320x480-75.jpg" />&nbsp;&nbsp;&nbsp;
                                             <img src="http://a4.mzstatic.com/us/r1000/099/Purple/v4/ec/73/53/ec73530d-f022-5c40-4c68-3bb79f7d572e/mzl.ayotrvek.320x480-75.jpg" width=160 alt="image"/>
                                        </div>
                             <p class="em_text">*** Free for limited time only ****
<br>
- Get Instant Alerts for NASDAQ stocks.
<br>
- Search from hundreds of stocks and set to receive alerts on our phone
<br>
                <br><br><a href='https://itunes.apple.com/us/app/free-instant-alerts-for-nasdaq/id642101022?ls=1&mt=8'>
	<img width=200 src="http://www.castlen.com/elements/App-Store-Badge.png"></a>
		</p>          
                    <div class="cleaner_h20"></div>
					<p><a href="#1" class="cross-link" title="Go to Page 1">&#171; Previous Page</a> | <a href="#3" class="cross-link" title="Go to Page 3">Next Page &#187;</a></p>
				</div>
			</div>		
			<div class="panel" title="Games">
				<div class="wrapper">
					
                     <h2>Sliding Puzzle With Adorable Puppies</h2>
                                                            <div class="image_wrapper fl_image">
                                            <img width=160 src="http://a2.mzstatic.com/us/r1000/106/Purple2/v4/df/3b/a9/df3ba91b-2595-9cd8-de60-b0f466d348e9/mzl.hanlljao.480x480-75.jpg" />&nbsp;&nbsp;&nbsp;
                                             <img src="http://a3.mzstatic.com/us/r1000/108/Purple2/v4/d2/11/fd/d211fde5-e9bf-5457-1415-5adb2f697e05/mzl.hfshynib.480x480-75.jpg" width=160 alt="image"/>
                                        </div>
                             <p class="em_text">
<p>New Puppies Added Weekly.</p>
<p>****FREE FOR LIMITED TIME ONLY****</p>
<p>Earn XP Points and collect Stars </p>
                <br><br><a href='https://itunes.apple.com/us/app/slide-puzzle-adorable-puppies/id648179171?mt=8'>
        <img width=200 src="http://www.castlen.com/elements/App-Store-Badge.png"></a>
</p>
                        <div class="cleaner"></div>
                     </div></div>
			<div class="panel" title="Educational">
				<div class="wrapper">
                    <h2>Projectile Motion - Angry Birds demystified</h2>
                    <div class="image_wrapper fl_image">
                            <img src="http://a1.mzstatic.com/us/r1000/114/Purple2/v4/df/63/34/df63347a-be3b-15ed-e623-d960fa303c8d/mzl.audsjlbt.320x480-75.jpg" alt="image 1"/>
                    </div>
                             <p class="em_text">Confused about where stuff land when you play Angry Birds? Play this app to launch flying objects at various angle and speed. Click and drag, just like when you do on Angry Bird, and see exactly how objects fly and land.
                <br><br><a href='https://itunes.apple.com/us/app/projectile-motion-free-edition/id644782348?mt=8'>
        <img width=200 src="http://www.castlen.com/elements/App-Store-Badge.png"></a>
</p>
                        <div class="cleaner"></div>
                     </div></div>
   
			<div class="panel" title="Contact Us">
				<div class="wrapper">
					
                    <h2>Contact Information</h2><?php echo $msg; ?>

                    <div id="contact_form">
                    
                        <form method="post" name="contact" action="">

                        <label for="author">Name:</label> <input type="text" id="author" name="author" class="required input_field" />
                        <div class="cleaner_h10"></div>
                        
                        <label for="email">Email:</label> <input type="text" id="email" name="email" class="validate-email required input_field" />
                        <div class="cleaner_h10"></div>
                        
                        <label for="subject">Subject:</label> <input type="text" name="subject" id="subject" class="input_field" />
                        <div class="cleaner_h10"></div>
                        
                        <label for="text">Message:</label> <textarea id="text" name="text" rows="0" cols="0" class="required"></textarea>
                        <div class="cleaner_h10"></div>
<input type='hidden' name='submit_button' value=1>                        
                        <input style="font-weight: bold;" type="submit" class="submit_btn" name="submit" id="submit" value=" Send " />
                        <input style="font-weight: bold;" type="reset" class="submit_btn" name="reset" id="reset" value=" Reset " />
                        
                        </form>
                    </div>

                    <div class="address_info">
                    	<h3>Office Location</h3>
                    	946 Highland Terrace<br>Sunnyvale, CA 94085<br>(650)804-6836
                    </div>
                    
                    <div class="cleaner_h20"></div>
                    
					<p><a href="#4" class="cross-link" title="Go to Page 4">&#171; Previous Page</a></p>
				</div>
			</div>
			
		</div><!-- .panelContainer -->
	</div><!-- #slider1 -->
</div><!-- .slider-wrap -->
<p id="cross-links" style="width:0px; height: 0px; font-size:0; overflow: hidden;">
	Same-page cross-link controls:<br />
	<a href="#1" class="cross-link">Page 1</a> | <a href="#2" class="cross-link">Page 2</a> | <a href="#3" class="cross-link">Page 3</a> | <a href="#4" class="cross-link">Page 4</a> | <a href="#5" class="cross-link">Page 5</a>
</p>

    <!-- end of slider -->
	</div> 
	<!-- end of templatemo_content -->
</div> <!-- end of templatemo_content_wrapper -->

<div id="templatemo_footer_wrapper">

	<div id="templatemo_footer">
	    Copyright © 2013 <a href="#">GrepAwk LLC</a>
        <div class="cleaner_h10"></div>
	</div> <!-- end of templatemo_footer -->
</div> <!-- end of templatemo_footer_wrapper -->
<div align=center>This template  downloaded form <a href='http://all-free-download.com/free-website-templates/'>free website templates</a></div></body>
</html>
