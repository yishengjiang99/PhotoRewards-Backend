<?
require_once("/var/www/lib/db.class.php");
$author=addslashes(urldecode($_GET['a']));
$rows=db::rows("select a.title, a.tid as tid, b.date from thread a join post b on a.op=b.pid where b.author='$author' order by b.date");
?>
<html>
<head>
 <script type="text/javascript" src="http://www.google.com/jsapi"></script>
</head>
<body>
<?php 
foreach($rows as $row){
$link="<a href=http://www.xoxohth.com/thread.php?thread_id=".$row['tid']."&mc=3&forum_id=2 target=_blank>".$row['title']."</a>";
 echo "<li>$link on (".$row['date'].")";
}?>
</body>
</html>
