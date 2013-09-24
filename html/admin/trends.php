<?
require_once("/var/www/lib/db.class.php");
$day=15;
if(isset($_GET['day'])){
 $day=intval($_GET['day']);
}
 $sql="select sum(revenue)/100 as r, unix_timestamp(min(created)) div 3600 * 3600 as t from sponsored_app_installs where created>date_sub(now(), interval $day day) group by month(created),day(created),hour(created)";
 $rows=db::rows($sql);

 $sql="select count(1) as user, unix_timestamp(min(created)) div 3600 * 3600 as t from appuser where created>date_sub(now(), interval $day day) and banned=0 and app='picrewards' group by month(created),day(created),hour(created)";
 $users=db::rows($sql);

 $tc=array();
 foreach($users as $ut){
	$tc[$ut['t']]=$ut['user'];
 }
$sql="select sum(CashValue) as gc, unix_timestamp(min(date_redeemed)) div 3600 * 3600 as t from rewards a join reward_codes b on a.id=b.reward_id where given_out=1 and date_redeemed>date_sub(now(), interval $day day) 
 group by unix_timestamp(date_redeemed) div 3600 * 3600";
$gc=db::rows($sql);

 $rc=array();
 foreach($gc as $gt){
   $rc[$gt['t']]=$gt['gc'];
 }
$sql="select sum(amount)/100 as pc, unix_timestamp(min(created)) div 3600 * 3600 as t from PaypalTransactions where status='processed' and created>date_sub(now(), interval $day day) group by month(created),day(created),hour(created)";
 $ppc=db::rows($sql);
 $pc=array();
 foreach($ppc as $pt){
   $pc[$pt['t']]=$pt['pc'];
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
        data.addColumn("number","gc");

	data.addRows([
<?php foreach($rows as $row){
  $t=$row['t'];
  $r=$row['r'];
  $tstr=$t."000";
  $u= isset($tc[$t]) ? $tc[$t] : 0;
  $g=isset($rc[$t]) ? $rc[$t] : 0;
  if(isset($pc[$t])) $g = $g+$pc[$t];
  echo "[new Date($tstr), $r,$u,$g],";
}?>]);
var chart = new google.visualization.AnnotatedTimeLine(document.getElementById('chart_div'));
 chart.draw(data, {displayAnnotations: true, dateFormat:"HH:mm MMMM dd, yyyy"});
});
</script>
</body>
</html>
