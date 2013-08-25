<html>
<body>
<style>
#mydiv{
    position:relative;
    overflow:hidden;
    height:1000px;
    width:600px;
}
#xoframe{
    position:absolute;
    top:0px;
    left:0px;
}
</style>

<table width=100%>
<tr><td height=1000px width=75%>
<div id=mydiv>
<iframe height=1000px width=1000px src='http://www.xoxohth.com' id=xoframe>
</iframe>
</div>
</td>
<td>
<div id=action>
</div>
</td>
</table>
</body>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.js"> </script>

<script>
$(document).ready(function(){
 $("#xoframe").load(function(){
 var iframe_doc = window.frames[0].document;
 alert(_this.contentWindow.document.body.scrollHeight);
  var h=$("#xoframe").contentWindow.document.body.scrollHeight;  
 $("#action").html(h);
 });
});
</script>
</html>
