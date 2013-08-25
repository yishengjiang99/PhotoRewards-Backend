<html>
<head>
  <title>Hello World</title>
   <script src='https://cdn.firebase.com/v0/firebase.js'></script>
    <script src='https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js'></script>
  <script src="https://www.firebase.com/js/libs/idle.js"></script>

</head>
<style>
#messagesDiv{height:500px; overflow-y:scroll}
</style>
<body>
<table><tr>
<td width=70%>
    <div id='messagesDiv'></div>
    <input type='text' id='nameInput' placeholder='Name' value='<? echo $g?>'> Says:
    <input type='text' id='messageInput' placeholder='Message' size=80>
</td>
<td width=20%>
<div id="presenceDiv"></div>
</td>
</tr>
</table>
<script>
      var myDataRef = new Firebase('https://t5cvjp2vrsy.firebaseio-demo.com/msg_<?php echo $_GET['room']; ?>');
  // Prompt the user for a name to use.
  var name = prompt("Your name?", "Guest"),
      currentStatus = "★ online";
  $("#nameInput").val(name);
  // Get a reference to the presence data in Firebase.
  var userListRef = new Firebase("https://t5cvjp2vrsy.firebaseio-demo.com/user'");
  
  // Generate a reference to a new location for my user with push.
  var myUserRef = userListRef.push();

  // Get a reference to my own presence status.
  var connectedRef = new Firebase("http://presence.firebaseio-demo.com/.info/connected");
  connectedRef.on("value", function(isOnline) {
    if (isOnline.val()) {
      // If we lose our internet connection, we want ourselves removed from the list.
      myUserRef.onDisconnect().remove();

      // Set our initial online status.
      setUserStatus("★ online");
    } else {

      // We need to catch anytime we are marked as offline and then set the correct status. We
      // could be marked as offline 1) on page load or 2) when we lose our internet connection
      // temporarily.
      setUserStatus(currentStatus);
    }
  });

  // A helper function to let us set our own state.
  function setUserStatus(status) {
    // Set our status in the list of online users.
    currentStatus = status;
    myUserRef.set({ name: name, status: status });
  }

  // Update our GUI to show someone"s online status.
  userListRef.on("child_added", function(snapshot) {
    var user = snapshot.val();
    $("#presenceDiv").append($("<div/>").attr("id", snapshot.name()));
    $("#" + snapshot.name()).text(user.name + " from ip (<?php echo $ip; ?>) is currently " + user.status);
  });

  // Update our GUI to remove the status of a user who has left.
  userListRef.on("child_removed", function(snapshot) {
    $("#" + snapshot.name()).remove();
  });

  // Update our GUI to change a user"s status.
  userListRef.on("child_changed", function(snapshot) {
    var user = snapshot.val();
    $("#" + snapshot.name()).text(user.name + " is currently " + user.status);
  });
     
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
  // Use idle/away/back events created by idle.js to update our status information.
  document.onIdle = function () {
    setUserStatus("☆ idle");
  }
  document.onAway = function () {
    setUserStatus("☄ away");
  }
  document.onBack = function (isIdle, isAway) {
    setUserStatus("★ online");
  }

  setIdleTimeout(5000);
  setAwayTimeout(10000);
</script>
</body>

</html>


