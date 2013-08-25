
<?
require_once("/var/www/lib/db.class.php");
$author=addslashes(urldecode($_GET['a']));
require_once("exclude.php");

$res=isset($_GET['res']) ? $_GET['res'] : 3600;
$rows=db::rows("select count(1) as cnt,unix_timestamp(date) div $res * $res as time from post where author='$author' and date>date_sub(now(), interval 60 day) group by unix_timestamp(date) div $res * $res");
?>
<html>
<head>
<title>Xoxohth meta data </title>
<meta name="description" content="xoxohth poster word cloud" />
<meta name="keywords" content="xoxohth" />
<li><a href="/xo2/threads.php?a=<? echo urlencode($_GET['a']) ?>">Threads</a>
<li><a href="/xo2/wordcloud.php?a=<? echo urlencode($_GET['a']) ?>">word cloud</a><br><br>
<li><a href="/xo2/alias.php?a=<? echo urlencode($_GET['a']) ?>">Aliases</a>
 <script type="text/javascript" src="/js/jsapi"></script>
</head>
<body>
<h1><?= $res ?> second interval posting count for <br>< <? echo $author ?> >in the last 55 days</h1>
<div id='chart_div' style='width: 700px; height: 240px;'>
</div>
<a href='/xo2/post.php?res=86400&a=<?= $_GET['a'] ?>'>day-by-day</a>
<script>
 google.load("visualization", "1", {packages:["annotatedtimeline"]});
 google.setOnLoadCallback(function(){
 var data = new google.visualization.DataTable();
	data.addColumn('date', "Date");
	data.addColumn("number", "poast");
	data.addRows([
<?php foreach($rows as $row){
 echo "[new Date(".$row['time']."000), ".$row['cnt']."],";
}?>]);
var chart = new google.visualization.AnnotatedTimeLine(document.getElementById('chart_div'));
 chart.draw(data, {displayAnnotations: true, dateFormat:"HH:mm MMMM dd, yyyy"});
});
</script>
</body>
</html>
