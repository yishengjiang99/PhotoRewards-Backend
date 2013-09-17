<?
require_once("/var/www/lib/db.class.php");
 $sql="select sum(revenue)/100 as r, unix_timestamp(min(created)) div 3600 * 3600 as t from sponsored_app_installs where created>date_sub(now(), interval 37 day) group by month(created),day(created),hour(created)";
 $rows=db::rows($sql);
 $sql="select count(1) as user, unix_timestamp(min(created)) div 3600 * 3600 as t from appuser where created>date_sub(now(), interval 37 day) and banned=0 and app='picrewards' group by month(created),day(created),hour(created)";
 $users=db::rows($sql);
 
 $tc=array();
 foreach($users as $ut){
	$tc[$ut['t']]=$ut['user'];
 }
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
	data.addColumn("number", "rev");
	data.addColumn("number","newuser");
	data.addRows([
<?php foreach($rows as $row){
  $t=$row['t'];
  $r=$row['r'];
  $tstr=$t."000";
  $u= isset($tc[$t]) ? $tc[$t] :0;
 echo "[new Date($tstr), $r,$u],";
}?>]);
var chart = new google.visualization.AnnotatedTimeLine(document.getElementById('chart_div'));
 chart.draw(data, {displayAnnotations: true, dateFormat:"HH:mm MMMM dd, yyyy"});
});
</script>
</body>
</html>
