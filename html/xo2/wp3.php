<?
require_once("/var/www/lib/db.class.php");
$author=addslashes(urldecode($_GET['a']));
$rows=db::rows("select a.title as text,a.tid from thread a join post b on a.op=b.pid where b.author='$author' order by b.date");
$rows=array_merge($rows,db::rows("select content as text, tid from post where author='$author'"));
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
 $list[]=array("text"=>$text,"weight"=>$weight,"link"=>"/xo/psearch.php?a=$author&tids=".$threads[$text]);
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

<li><a href="/xo">Index</a>
<li><a href="/xo/post.php?a=<? echo urlencode($_GET['a']) ?>">posts</a>
<li><a href="/xo/threads.php?a=<? echo urlencode($_GET['a']) ?>">threads</a><br><br>

<div id="wordcloud" style="width: 900px; height: 700px; position: relative;"></div>
skipping <i><?php echo implode(", ",$skip) ?></i>
<br>you can skip certain words by appending "&skip=had,go,if", a comma-separated list of words to skip to to the url.
<script>
$(document).ready(function() {
var json='<? echo  json_encode($list) ?>';
  var wordlist=$.parseJSON(json);
  $("#wordcloud").jQCloud(wordlist);  
});
</script>
