<?
require_once("/var/www/lib/db.class.php");
$author=addslashes(urldecode($_GET['a']));
require_once("exclude.php");
$aliases=db::cols("select original_name from aliases where new_name='$author'");
$aliases=array_merge($aliases, db::cols("select new_name from aliases where original_name='$author'"));
$aliasthreads=array();
?>
<html>
<head>
<title>Aliases of <?=$author ?> </title>
<meta name="description" content="xoxohth poster" />
<meta name="keywords" content="xoxohth" />
 <script type="text/javascript" src="http://www.google.com/jsapi"></script>
</head>
<body>
<iframe src="//api.virool.com/widgets/5c0dbeeee932e5ad448fcdbc01121b3e/21554?width=640&height=360&pxtrackback=http://json999.com/cb.php" width="640" height="360" allowfullscreen marginwidth="0" marginheight="0" frameborder="0" scrolling="no"></iframe>
<?foreach($aliases as $alias){?>
<? echo "<h3>$alias</h3>"; ?>
<li><a href="/xo2/threads.php?a=<? echo urlencode($alias) ?>">threads</a>

<li><a href="/xo2/post.php?a=<? echo urlencode($alias) ?>">posts</a>
<li><a href="/xo2/wordcloud.php?a=<? echo urlencode($alias) ?>">word cloud</a><br><br>
<?}?>
<?php 
foreach($aliasthreads as $alias=>$rows){
echo "<h1> threads by $alias</h1>";
foreach($rows as $row){
$link="<a href=http://www.xoxohth.com/thread.php?thread_id=".$row['tid']."&mc=3&forum_id=2 target=_blank>".$row['title']."</a>";
 echo "<li>$link on (".$row['date'].")";
}
}?>
</body>
</html>
