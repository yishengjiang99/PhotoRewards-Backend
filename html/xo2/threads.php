<?
require_once("/var/www/lib/db.class.php");
$author=addslashes(urldecode($_GET['a']));
$a=$author;
require_once("exclude.php");
$rows=db::rows("select a.title, a.tid as tid, b.date from thread a join post b on a.op=b.pid where b.author='$author' order by b.date");
$aliases =db::cols("select new_name from aliases where original_name='$author'");
$aliases = array_merge($aliases, db::cols("select original_name from aliases where new_name='$author'")); 
?>
<html>
<head>
<title>Xoxohth.com threads by <?=$author?></title>
<meta name="description" content="xoxohth poster" />
<meta name="keywords" content="xoxohth threads by <?=$author?>" />

 <script type="text/javascript" src="http://www.google.com/jsapi"></script>
</head>
<body>
<li><a href="/xo2/post.php?a=<? echo urlencode($_GET['a']) ?>">posts</a>
<li><a href="/xo2/wordcloud.php?a=<? echo urlencode($_GET['a']) ?>">word cloud</a>
<li><a href="/xo2/alias.php?a=<? echo urlencode($_GET['a']) ?>">Aliases</a>
<li>AKA:<br> <? echo implode("<br>",$aliases) ?>
<br><br>
<?php 
foreach($rows as $row){
if(rand(0,3)==1) echo '<br><a href="http://www.tkqlhce.com/click-6500692-10277142" target="_top">Don\'t stand in the line - buy the New York Pass online</a><img src="http://www.lduhtrp.net/image-6500692-10277142" width="1" height="1" border="0"/>';
$link="<a href=http://www.xoxohth.com/thread.php?thread_id=".$row['tid']."&mc=3&forum_id=2 target=_blank>".$row['title']."</a>";
 echo "<li>$link on (".$row['date'].")";
}?>
</body>
</html>
