<?php
require_once("/var/www/lib/functions.php");
$gfz=file_get_contents("/var/www/html/stag/gz.txt");
$gfza=explode("\n",$gfz);
$valcmd='';
$posts=array();
$vals=array();
$goods=array();
$fits=array();$trainfits=array();
for($i=0;$i<88;$i++){
 $good=$gfza[$i*4+1];
 $goods[]=$good;
 $valcmd.='val '.$good.'\n';
 $posts[$good]=$gfza[$i*4];
 $fits[$good]=$gfza[$i*4+3];
//echo "<br>".$fits[$good];
 $trainfits[$good]=$gfza[$i*4+2];
}
$errormsg="";
$output="";
if(isset($_POST['formpost'])){
 $tp=$_POST['postname'];
 $txt=$_POST['valout'];
 preg_match_all("/pay you ([0-9]+) gold/",$txt,$m);
 $vals=$m[1];
 if(count($vals)!=88){
  $errormsg="You pasted something but it's not exactly 88 strings of 'We will pay you ... gold'..";
 }else{
  foreach($m[1] as $i=>$payout){
   $good=$goods[$i];
   $from=$posts[$good];
   $fitwag=$fits[$good];
   $train=$trainfits[$good];
   $wagonprofit=$payout*$fitwag;
   $trainprofit=$payout*$train;
   $output.="<br>$good [$from] pays [$payout] <b>".number_format($wagonprofit)."</b> for wagon and <b>".number_format($trainprofit)."</b> for trains";
  }   
  if(isset($_POST['share']) && $_POST['postname']!='none'){
     $fname=$_POST['postname']."_".date("Y-m-d H:i:s")."_cached.html";
     file_put_contents("/var/www/html/stag/cached/$fname",$output);
     file_put_contents("/var/www/html/stag/cached/cindex",$fname."\n".file_get_contents("/var/www/html/stag/cached/cindex"));
   }
 }
}
?>
<html>
<head>
    <script src="js/jquery-1.2.1.pack.js" type="text/javascript"></script>
	<script src="/ZeroClipboard.js" type="text/javascript"></script>
</head>
<body>
<h3>Medievia TR Calculator for Telnet + community info share</h3>
<table>
<tr><td width=50% valign=top>
<form method=POST action='tr.php'>
<li>Step 1: fly to an trading post: <select name=postname><option value=none>select tp</option>
<? foreach(array_unique(array_values($posts)) as $tp){
$tp=urlencode($tp);
echo "<option value=$tp>".urldecode($tp)."</option>";
}?>
</select>
<li>Step 2: <div id='ccccont' onMouseOver="move_swf(this)" onMouse></div>
<li>Step 3: paste into telnet or other medievia clients
<li>step 4: copy/paste everything tp dude says "We will pay you xxx gold for each crate/bundle/case etc" (unfortunately, you'd have to paste everything otherwise the index gets thrown off. you can paste other stuff in between, we'll do the filtering)
<br>
<textarea cols=80 rows=20 placeholder='paste here' name=valout>
</textarea>
<input type='hidden' name='formpost' value=1 />
<br><input type='checkbox' name='share' CHECKED />Share this data for karma points
<br><input type='submit' value='calc trade vals'>
</form>
<div style='height:300px; overflow-y:scroll'>
Previously shared data
<?php
$cacheindex=file_get_contents("/var/www/html/stag/cached/cindex");
$ct=explode("\n",$cacheindex);
foreach($ct as $c){
echo "<br><a href='/cached/$c' target=_blank>".urldecode($c)."</a>";
}
?>
</div>
</td><td width=50% valign=top>
<?php echo $errormsg."<br>".$output; ?>
</td></tr></table>
<script>
function move_swf(ee) {    
// $(ee).css("background-color","black");
}    

$(document).ready(function(){
 $("#ccccont").html(getCopyLinkHTML('40px', '34px'));
        var clip = new ZeroClipboard.Client();
        clip.setText("<?php echo $valcmd ?>");
        clip.setHandCursor(true);
        clip.setCSSEffects(true);
     clip.glue('d_clip_button', 'd_clip_container');
   clip.addEventListener( 'onComplete', my_complete );
});
function my_complete(ee,c){
alert('copied; go to med and paste');
}
function getCopyLinkHTML(left, top) {
return '<div id="d_clip_container" style="left: ' + left + '; top: ' + top + '; width:auto;border:1px solid black; padding:10px;width:300px"><div id="d_clip_button">Click here to Copy Commands to value everything;</div></div>';
} 
</script>
</body>
