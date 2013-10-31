<?
require_once("/var/www/lib/db.class.php");
$day=15;
$res=3600;
if(isset($_GET['day'])){
 $day=intval($_GET['day']);
}
if(isset($_GET['res'])){
 $res=intval($_GET['res']);
}
 $sql="select sum(revenue)/100 as r, unix_timestamp(min(created)) div $res * $res as t from sponsored_app_installs where created>date_sub(now(), interval $day day) group by unix_timestamp(created) div $res * $res";
 $rows=db::rows($sql);

 $sql="select count(1) as user, unix_timestamp(min(created)) div $res * $res as t from appuser where created>date_sub(now(), interval $day day) and banned=0 and app='picrewards' group by unix_timestamp(created) div $res * $res";
 $users=db::rows($sql);

 $tc=array();
 foreach($users as $ut){
	$tc[$ut['t']]=$ut['user'];
 }
 $sql="select count(1) as joiners, unix_timestamp(min(created)) div $res * $res as t from referral_bonuses where created>date_sub(now(), interval $day day) group by unix_timestamp(created) div $res * $res";
 $joiners=db::rows($sql);
 $jc=array();
 foreach($joiners as $jt){
    $jc[$jt['t']]=$jt['joiners'];
 }

$sql="select sum(CashValue) as gc, unix_timestamp(min(date_redeemed)) div $res * $res as t from rewards a join reward_codes b on a.id=b.reward_id where given_out=1 and date_redeemed>date_sub(now(), interval $day day) 
 group by unix_timestamp(date_redeemed) div $res * $res";
$gc=db::rows($sql);

 $rc=array();
 foreach($gc as $gt){
   $rc[$gt['t']]=$gt['gc'];
 }

$sql="select sum(amount)/100 as pc, unix_timestamp(min(created)) div $res * $res as t from PaypalTransactions where status='processed' and created>date_sub(now(), interval $day day) group by unix_timestamp(created) div $res * $res";
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
<a href='/admin/trends.php?day=7&res=3600'>7 day hourly</a><br>

<a href='/admin/trends.php?day=30&res=3600'>30 day hourly</a><br>
<a href='/admin/trends.php?day=90&res=3600'>90 day hourly</a><br>

<a href='/admin/trends.php?day=30&res=86400'>30 day daily</a><br>
<a href='/admin/trends.php?day=90&res=86400'>90 day daily</a> <br>

<div id='chart_div' style='width: 700px; height: 240px;'>
</div>
<div id='chart_div2' style='width: 700px; height: 240px;'>
</div>
<script>
 google.load("visualization", "1", {packages:["annotatedtimeline"]});
 google.setOnLoadCallback(function(){
 var data = new google.visualization.DataTable();
	data.addColumn('date', "Date");
	data.addColumn("number", "rev");
        data.addColumn("number","gc");
	data.addRows([
<?php foreach($rows as $row){
  $t=$row['t'];
  $r=$row['r'];
  $tstr=$t."000";
  $u= isset($tc[$t]) ? $tc[$t] : 0;
  $g=isset($rc[$t]) ? $rc[$t] : 0;
  if(isset($pc[$t])) $g = $g+$pc[$t];
  echo "[new Date($tstr), $r,$g],";
}?>]);
var chart = new google.visualization.AnnotatedTimeLine(document.getElementById('chart_div'));
 chart.draw(data, {displayAnnotations: true, dateFormat:"HH:mm MMMM dd, yyyy"});

 var data2 = new google.visualization.DataTable();
        data2.addColumn('date', "Date");
        data2.addColumn("number", "newusers");
        data2.addColumn("number","joiners");
        data2.addRows([
<?php foreach($users as $row){
  $t=$row['t'];
  $u=$row['user'];
  $tstr=$t."000";
  $j=isset($jc[$t]) ? $jc[$t] : 0;
  echo "[new Date($tstr), $u,$j],";
}?>]);
var chart2 = new google.visualization.AnnotatedTimeLine(document.getElementById('chart_div2'));
 chart2.draw(data2, {displayAnnotations: true, dateFormat:"HH:mm MMMM dd, yyyy"});


});
</script>
</body>
</html>
