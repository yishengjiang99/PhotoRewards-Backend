<?php 
$g='guest';
?>
<html>
<head>
  <script src="https://cdn.firebase.com/v0/firebase.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
  <link rel="stylesheet" type="text/css" href="https://www.firebase.com/css/example.css">
</head>
<body style=width:100%>
<table width=100%>
<tr>
<td width=40%>
    <div id='messagesDiv'></div>
    <input type='text' id='nameInput' placeholder='Name' value='<? echo $g?>'> Says:
    <input type='text' id='messageInput' placeholder='Message' size=80>
</td>
<td width=60%>
<div style='margin-left:10px'>
  <canvas id="drawing-canvas" width="480" height="420"></canvas>
</div>
<div style='margin-left:10px' id="colorholder"></div>
Brush Size:<select id=brush><option value=8>8px</option><option value=3>3px</option><option value=10>10px</option><option value=15>15px</option><option value=55>55px</option></select>
</table>
<script>
  $(document).ready(function () {
      var myDataRef = new Firebase('https://t5cvjp2vrsy.firebaseio-demo.com/msg');
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
   //Set up some globals
    var pixSize = 8, lastPoint = null, currentColor = "000", mouseDown = 0;
	
    //Create a reference to the pixel data for our drawing.
    var pixelDataRef = new Firebase('https://sor0d9kxrxi.firebaseio-demo.com/');
    // Set up our canvas
    var myCanvas = document.getElementById('drawing-canvas');
    var myContext = myCanvas.getContext ? myCanvas.getContext('2d') : null;
    if (myContext == null) {
      alert("You must use a browser that supports HTML5 Canvas to run this demo.");
      return;
    }

    //Setup each color palette & add it to the screen
    var colors = ["fff","000","f00","0f0","00f","88f","f8d","f88","f05","f80","0f8","cf0","08f","408","ff8","8ff"];
    for (c in colors) {
      var item = $('<div/>').css("background-color", '#' + colors[c]).addClass("colorbox");
      if(c==0){
         item.html("<img width=20 height=20 src=http://json999.com/eraser.png>");
       }
      item.click((function () {
        var col = colors[c];
        return function () {
          currentColor = col;
        };
      })());
      item.appendTo('#colorholder');
    }

    //Keep track of if the mouse is up or down
    myCanvas.onmousedown = function () {mouseDown = 1;};
    myCanvas.onmouseout = myCanvas.onmouseup = function () {
      mouseDown = 0; lastPoint = null;
    };

    //Draw a line from the mouse's last position to its current position
    var drawLineOnMouseMove = function(e) {
      if (!mouseDown) return;

      e.preventDefault();

      // Bresenham's line algorithm. We use this to ensure smooth lines are drawn
      var offset = $('canvas').offset();
      var x1 = Math.floor((e.pageX - offset.left) / pixSize - 1),
        y1 = Math.floor((e.pageY - offset.top) / pixSize - 1);
      var x0 = (lastPoint == null) ? x1 : lastPoint[0];
      var y0 = (lastPoint == null) ? y1 : lastPoint[1];
      var dx = Math.abs(x1 - x0), dy = Math.abs(y1 - y0);
      var sx = (x0 < x1) ? 1 : -1, sy = (y0 < y1) ? 1 : -1, err = dx - dy;
      while (true) {
        //write the pixel into Firebase, or if we are drawing white, remove the pixel
        pixelDataRef.child(x0 + ":" + y0).set(currentColor === "fff" ? null : currentColor);
        if (x0 == x1 && y0 == y1) break;
        var e2 = 2 * err;
        if (e2 > -dy) {
          err = err - dy;
          x0 = x0 + sx;
        }
        if (e2 < dx) {
          err = err + dx;
          y0 = y0 + sy;
        }
      }
      lastPoint = [x1, y1];
    };
    $(myCanvas).mousemove(drawLineOnMouseMove);
    $(myCanvas).mousedown(drawLineOnMouseMove);

    // Add callbacks that are fired any time the pixel data changes and adjusts the canvas appropriately.
    // Note that child_added events will be fired for initial pixel data as well.
    var drawPixel = function(snapshot) {
      var coords = snapshot.name().split(":");
      myContext.fillStyle = "#" + snapshot.val();
      myContext.fillRect(parseInt(coords[0]) * pixSize, parseInt(coords[1]) * pixSize, pixSize, pixSize);
    };
    var clearPixel = function(snapshot) {
      var coords = snapshot.name().split(":");
      myContext.clearRect(parseInt(coords[0]) * pixSize, parseInt(coords[1]) * pixSize, pixSize, pixSize);
    };
    pixelDataRef.on('child_added', drawPixel);
    pixelDataRef.on('child_changed', drawPixel);
    pixelDataRef.on('child_removed', clearPixel);
  });
</script>
</body>
</html>
