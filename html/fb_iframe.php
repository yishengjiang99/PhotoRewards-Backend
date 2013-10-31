<?php
$msg="Sign in with Facebook";
$appid='146678772188121'; //picrewards
if(isset($_GET['appid'])){
 $appid=$_GET['appid'];
}
if(isset($_GET['msg'])){
 $msg=urldecode($_GET['msg']);
}
?>
<html>
<head>
    <link rel="stylesheet" href="/css/auth-buttons.css"> 
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.js"> </script>
<style>
.emp{
    color: #FFFC5A;
    font-size: 22px;
    font-weight: normal;
    margin: 0 0 20px;
    padding: 3px 0;
}</style>
</head>
<body>
<div id="fb-root"></div>
<span>
<a id=signin class="btn-auth btn-facebook large" href="#">
 <?php echo $msg; ?>
</a>
</span>
<script>

window.fbAsyncInit = function() {
  FB.init({
    appId      : '<?= $appid ?>',
    channelUrl : '//www.json999.com/channel.html', // Channel File
    status     : true, // check login status
    cookie     : true, // enable cookies to allow the server to access the session
  });

  FB.Event.subscribe('auth.authResponseChange', function(response) {
    // Here we specify what we do with the response anytime this event occurs. 
    if (response.status === 'connected') {
	connected();
    } else if (response.status === 'not_authorized') {
	 $("#signin").show();
    } else {
	 $("#signin").show();
    }
  });
}

  $("#signin").click(function(){
     fblogin();
  });

 function fblogin(){
        FB.login(function(response) {
            if (response.session) {
                if (response.perms) {
                        connected();
                } else {
                    // user is logged in, but did not grant any permissions
                }
            } else {
                // user is not logged in
            }
	}, {scope:'email,user_likes'});
  }
  // Load the SDK asynchronously
  (function(d){
   var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
   if (d.getElementById(id)) {return;}
   js = d.createElement('script'); js.id = id; js.async = true;
   js.src = "//connect.facebook.net/en_US/all.js";
   ref.parentNode.insertBefore(js, ref);
  }(document));
  function connected() {
    FB.api('/me', function(response) {
      $.post("/fbreg.php",{data:response},function(){
	$("#signin").removeClass("btn-auth btn-facebook large").parent().html("Welcome "+response.first_name).addClass("emp").show();
	});
    });
  }

</script>
</body>
</html>
