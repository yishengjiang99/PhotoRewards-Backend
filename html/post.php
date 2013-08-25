<?
$levels =  exec("tail -n 300 /var/www/battery_level", $out);
?>
<html>
<head>
<title>Xoxohth meta data </title>
 <script type="text/javascript" src="/js/jsapi"></script>
</head>
<body>
<h1>
<div id='chart_div' style='width: 700px; height: 240px;'>
</div>
<script>
 google.load("visualization", "1", {packages:["annotatedtimeline"]});
 google.setOnLoadCallback(function(){
 var data = new google.visualization.DataTable();
	data.addColumn('date', "Date");
	data.addColumn("number", "Battery Level");
	data.addRows([
<?php foreach($out as $level){
 if(trim($level)=="") continue;
 $t=explode(" ",$level);
 $time=$t[0];
 $level=doubleVal($t[1]);
 echo "[new Date(".$time."000), ".$level."],";
}?>]);
var chart = new google.visualization.AnnotatedTimeLine(document.getElementById('chart_div'));
 chart.draw(data, {displayAnnotations: true, dateFormat:"HH:mm MMMM dd, yyyy"});
});
</script>
</body>
</html>
