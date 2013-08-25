

<html>
<head>
  <title>Hello World</title>
   <script src='https://cdn.firebase.com/v0/firebase.js'></script>
    <script src='https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js'></script>

</head>
<body>
<style>
  body.connected #login { display: none; }
  body.connected #logout { display: block; }
  body.not_connected #login { display: block; }
      
  body.not_connected #logout { display: none; }
</style>
    <div id='messagesDiv'></div>
   <input type='text' id='nameInput' placeholder='Name'>
    <input type='text' id='messageInput' placeholder='Message'>

<div id="fb-root"></div>
 <div id="user-info"></div>
<div id="login">
   <p><button onClick="loginUser();">Login</button></p>
 </div>
 <div id="logout">
   <p><button  onClick="FB.logout();">Logout</button></p>
 </div>
<a href="#" onclick="sendRequest();">Send request</a><br>
<a href="#" onclick="getUserFriends();">Get friends</a><br>
 
<div id="user-friends"></div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js"></script>
    
<script>
      var myDataRef = new Firebase('https://t5cvjp2vrsy.firebaseio-demo.com/');
      $('#messageInput').keypress(function (e) {
        if (e.keyCode == 13) {
          var name = $('#nameInput').val();
          var text = $('#messageInput').val();
	  myDataRef.push({name: name, text: text});
          $('#messageInput').val('');
        }
      });
myDataRef.on('child_added', function(snapshot) {
var message = snapshot.val();
displayChatMessage(message.name, message.text);
});
      function displayChatMessage(name, text) {
        $('<div/>').text(text).prepend($('<em/>').text(name+': ')).appendTo($('#messagesDiv'));
        $('#messagesDiv')[0].scrollTop = $('#messagesDiv')[0].scrollHeight;
      };
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '214712071974494', // App ID
      channelUrl : '//www.json999.com/fbchannel.html', // Channel File
      status     : true, // check login status
      cookie     : true, // enable cookies to allow the server to access the session
      xfbml      : true  // parse XFBML
    });
    FB.Event.subscribe('auth.statusChange', handleStatusChange);
  };

  // Load the SDK Asynchronously
  (function(d){
     var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement('script'); js.id = id; js.async = true;
     js.src = "//connect.facebook.net/en_US/all.js";
     ref.parentNode.insertBefore(js, ref);
   }(document));
   	
   function loginUser() {    
     FB.login(function(response) { }, {scope:'email'});     
     }
   function handleStatusChange(response) {    
  	document.body.className = response.authResponse ? 'connected' : 'not_connected';
      if (response.authResponse) {
        console.log(response);
        updateUserInfo(response);
      }
    }
   function updateUserInfo(response) {
     FB.api('/me', function(response) {
       document.getElementById('user-info').innerHTML = '<img src="https://graph.facebook.com/' + response.id + '/picture">' + response.name;
     });
   }

  function getUserFriends() {
   FB.api('/me/friends?fields=name,picture', function(response) {
     console.log('Got friends: ', response);
     if (!response.error) {
       var markup = '';
       var friends = response.data;
       for (var i=0; i < friends.length && i < 25; i++) {
         var friend = friends[i];
         markup += '<img src="' + friend.picture.data.url + '"> ' + friend.name + '<br>';
       }
       document.getElementById('user-friends').innerHTML = markup;
     }
   });
 }
function sendRequest() {
  FB.ui({
    method: 'apprequests',
    message: 'invites you to learn how to make your mobile web app social',
  }, 
  function(response) {
    console.log('sendRequest response: ', response);
  });
}
</script>
</body>

</html>


