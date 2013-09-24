<html>
<body>
search stock symbols on 5 sites!
<form method=POST>
<input type=text name=s>
<input type=submit value=search>
</form>
<br>
<?php
$sites=explode("\n","http://research.investors.com/ibd-charts.aspx?cht=pvc&type=daily&symbol=%s&src=
http://finviz.com/quote.ashx?t=%s&ty=l&ta=0&p=i5&b=1
http://seekingalpha.com/symbol/%s/currents
https://www.tradingview.com/chart/%s/
https://www.google.com/finance?chdnp=1&chdd=1&chds=1&chdv=1&chvs=logarithmic&chdeh=0&chfdeh=0&chdet=1379016000000&chddm=391&chls=IntervalBasedLine&q=%s&&fct=big&ei=");
if(isset($_POST['s'])){
$searchTerm=$_POST['s'];
foreach($sites as $s){
  $url=str_replace("%s",$searchTerm,$s);
  echo "<iframe src=$url width=48% height=500px></iframe>";
 }
}
?>

