<?
require_once("/var/www/lib/db.class.php");
$res=3600;
if(isset($_GET['res'])) $res= intval($_GET['res']);
 $sql="select sum(m) as v, unix_timestamp(t) div $res * $res as t from app_event where t>date_sub(now(), interval 24 hour) and name like 'applist%' group by unix_timestamp(t) div $res";
 $rows=db::rows($sql); 
echo $sql;
?>
<html>
<head>
<title>trends</title>
 <script type="text/javascript" src="/js/jsapi"></script>
</head>
<body>
<div id='chart_div' style='width: 700px; height: 240px;'>
</div>
<script>
 google.load("visualization", "1", {packages:["annotatedtimeline"]});
 google.setOnLoadCallback(function(){
 var data = new google.visualization.DataTable();
	data.addColumn('date', "Date");
	data.addColumn("number", "e");
	data.addRows([
<?php foreach($rows as $row){
  $t=$row['t'];
  $r=$row['v'];
  $tstr=$t."000";
 echo "[new Date($tstr), $r,$v],";
}?>]);
var chart = new google.visualization.AnnotatedTimeLine(document.getElementById('chart_div'));
 chart.draw(data, {displayAnnotations: true, dateFormat:"HH:mm MMMM dd, yyyy"});
});
</script>
</body>
</html>
