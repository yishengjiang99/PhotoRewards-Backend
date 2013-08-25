<?
require_once("/var/www/lib/db.class.php");
$author=addslashes(urldecode($_GET['a']));
require_once("exclude.php");

$rows=db::rows("select a.title as text,a.tid from thread a join post b on a.op=b.pid where b.author='$author' order by b.date");
$p=0;
$_min=0;
if(isset($_GET['p']) && $_GET['p']==1){
$p=1;
$_min=4;
 $rows=array_merge($rows,db::rows("select content as text, tid from post where author='$author'"));
}
$words=array();
$skip=array('the','on','in','for','do','is','an','to','by','did','at','this','a','of','are','and','with');
if(isset($_GET['skip'])){
 $skip=array_merge($skip,explode(",",$_GET['skip']));
}
$threads=array();

foreach($rows as $row){
 $toks=explode(" ",$row['text']);
 foreach($toks as $t){
 $t=ereg_replace("[^A-Za-z0-9]", "",strtolower($t));
   if(in_array($t,$skip)) continue;
  if(!isset($words[$t])){
    $words[$t]=0;
    $threads[$t]="";
  }
  $threads[$t].=$row['tid'].",";
  $words[$t]++;
 }
}
$list=array();
$min=isset($_GET['min']) ? $_GET['min'] : $_min;
foreach($words as $text=>$weight){
 if($weight<$min) continue;
 $list[]=array("text"=>$text,"weight"=>$weight,"link"=>"/xo2/psearch.php?a=$author&tids=".$threads[$text]);
}
?>
<html>
<head>
<title>Xoxohth meta data </title>
<meta name="description" content="xoxohth poster word cloud" />
<meta name="keywords" content="xoxohth" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.js"> </script>
<script type="text/javascript" src="http://www.json999.com/js/jqcloud-1.0.3.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="/css/jqcloud.css" />	
</head>
<body>
<iframe src="//api.virool.com/widgets/5c0dbeeee932e5ad448fcdbc01121b3e/21554?width=640&height=360&pxtrackback=http://json999.com/cb.php" width="640" height="360" allowfullscreen marginwidth="0" marginheight="0" frameborder="0" scrolling="no"></iframe>
<a target="_blank" href="http://affiliate.godaddy.com/redirect/B6E9B9E638255A31B14206FF531379094981616A59F4E2AF3D92D47AEDF9371134C03B9985E561C2C62D6CCC89F438DAB6FE6F416EAB410B90589EDA2235243B"><img src="http://affiliate.godaddy.com/ads/B6E9B9E638255A31B14206FF531379094981616A59F4E2AF3D92D47AEDF9371134C03B9985E561C2C62D6CCC89F438DAB6FE6F416EAB410B90589EDA2235243B" border="0" width="200"  height="200" alt="Go Daddy Deal of the Week: 35% off your first order on GoDaddy.com! Offer expires 1/3/13."/></a>
Search For Poster: <form method=GET>
<input type=text name=a />
<input type=submit />
</form>
<h3>Frequently used words in <?=$author?>'s Thread titles</h3>
<li><a href="/xo">Index</a>
<li><a href="/xo2/rate_threads.php">Rate Threads (<font color=red>New</font>)</a>
<li><a href="/xo2/post.php?a=<? echo urlencode($_GET['a']) ?>">posts</a>
<li><a href="/xo2/threads.php?a=<? echo urlencode($_GET['a']) ?>">threads</a><br><br>
<?
if($p==0){
echo "<b><a href='?p=1&a=".urlencode($_GET['a'])."&min=10'>Include post/reply contents</a><br><br>";
}else{
 echo "<b><a href='?p=0&a=".urlencode($_GET['a'])."&min=10'>Include only thread titles</a><br><br>";
}
?>
<div id="wordcloud" style="width: 900px; height: 700px; position: relative;"></div>
<b><a href="/xo/wp.php?p=1&a=<? echo urlencode($_GET['a']) ?>">Include post/reply contents</a><br><br>
skipping <i><?php echo implode(", ",$skip) ?></i>
<br>you can skip certain words by appending "&skip=had,go,if", a comma-separated list of words to skip to to the url.
<script>
$(document).ready(function() {
var json='<? echo  json_encode($list) ?>';
  var wordlist=$.parseJSON(json);
  $("#wordcloud").jQCloud(wordlist);  
});
</script>
