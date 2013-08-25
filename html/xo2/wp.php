<?
require_once("/var/www/lib/db.class.php");
$author=addslashes(urldecode($_GET['a']));
require_once("exclude.php");

$rows=db::rows("select a.title as text,a.tid from thread a join post b on a.op=b.pid where b.author='$author' order by b.date");
$p=0;
if(isset($_GET['p']) && $_GET['p']==1){
$p=1;
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
$min=isset($_GET['min']) ? $_GET['min'] : 4;
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
<script type="text/javascript" src="https://raw.github.com/lucaong/jQCloud/master/jqcloud/jqcloud-1.0.2.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="/css/jqcloud.css" />	
</head>
<body>
Search For Poster: <form method=GET>
<input type=text name=a />
<input type=submit />
</form>
<h3>Frequently used words in <?=$author?>'s Thread titles</h3>
<li><a href="/xo2">Index</a>
<li><a href="/xo2/rate_threads.php">Rate Threads (<font color=red>New</font>)
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
<b><a href="/xo2/wp.php?p=1&a=<? echo urlencode($_GET['a']) ?>">Include post/reply contents</a><br><br>
skipping <i><?php echo implode(", ",$skip) ?></i>
<br>you can skip certain words by appending "&skip=had,go,if", a comma-separated list of words to skip to to the url.
<script>
$(document).ready(function() {
var json='<? echo  json_encode($list) ?>';
  var wordlist=$.parseJSON(json);
  $("#wordcloud").jQCloud(wordlist);  
});
</script>
