<?
var_dump($_COOKIE);
if(!isset($_COOKIE['udid']) && strpos($_SERVER['HTTP_USER_AGENT'],"iPhone")!==false){
  header("location: /start.php");
}
require_once("/var/www/lib/db.class.php");
$data=unserialize(file_get_contents("/var/www/cache/top100"));
if(!$data || $data['ttl']<time()){
	$rows=db::rows("select count(1) as cnt, author from post where date>date_sub(now(), interval 60 day) and author !=\"R A G N U S\" group by author order by cnt desc limit 100");
	$data=array("rows"=>$rows, "ttl"=>time()+60*60*24);
	file_put_contents("/var/www/cache/top100",serialize($data));
}
$rows=$data['rows'];

$data=unserialize(file_get_contents("/var/www/cache/top100today"));
if(true || !$data || $data['ttl']<time()){
	$todayrows=db::rows("select count(1) as cnt, author from post where date>date_sub(now(), interval 1 day) group by author order by cnt desc limit 100");
        $data=array("rows"=>$todayrows, "ttl"=>time()+60*60*2);
        file_put_contents("/var/www/cache/top100today",serialize($data));
}
$latestpumos=db::rows("select distinct(author) from post where author like '%.,%' order by pid desc limit 500");
$todayrows=$data['rows'];
$links=array("post.php?a=%A"=>"hour-by-hour", "threads.php?a=%A"=>"Started threads", "wordcloud.php?a=%A"=>"word cloud");
$aliases=db::cols("select * from aliases where original_name not like '%Subject%' and new_name not like '%</b>%'");
?>
<html>
<head>
<title>Xoxohth meta data </title>
<meta name="description" content="xoxohth poster word cloud" />
<meta name="keywords" content="xoxohth" />

</head>
<body>
<iframe src="//api.virool.com/widgets/5c0dbeeee932e5ad448fcdbc01121b3e/21554?width=640&height=360&pxtrackback=http://json999.com/cb.php" width="640" height="360" allowfullscreen marginwidth="0" marginheight="0" frameborder="0" scrolling="no"></iframe>
<!-- ClickTale Top part -->
<script type="text/javascript">
var WRInitTime=(new Date()).getTime();
</script>
<!-- ClickTale end of Top part -->

<li><a href="/xo/rate_threads.php">Rate Threads (<font color=red>New</font>)</a>

Search For Poster: <form action=search.php method=POST>
<input type=text name=q />
<input type=submit />
</form>
<table><tr>
<td valign=top>
<h1>Latest Pumos</h1>
<table border=1>
<tr><td>name</td></tr>
<?php foreach($latestpumos as $row){
 $a=$row['author'];
 $cnt="";
 $aenc=urlencode($a);
 if(in_array($a, $aliases)){
        $alliasLink="<br>(<a href='alias.php?a=$aenc'><font color=red>Possible Aliases</font></a>)";
 }else{
	continue;
       	$alliasLink="";
 }
 $linkshtml="";
 foreach($links as $href=>$display){
   $href=str_replace("%A",$aenc,$href);
  $linkshtml.="<li><a href='$href' target=_blank>$display</a>";
}
echo "<tr><td>$a $alliasLink</td></tr>";
}?>
</table>
</td>
<td>
<h1>Top posters today</h1>
<table border=1>
<tr><td>name</td><td>poast count</td><td>details</td></tr>
<?php foreach($todayrows as $row){
 $a=$row['author'];
 $cnt=$row['cnt'];
 $aenc=urlencode($a);
 if(in_array($a, $aliases)){
        $alliasLink="(<a href='alias.php?a=$aenc'><font color=red>Possible Aliases</font></a>)";
 }else{
	$alliasLink="";
 }
 $linkshtml="";
 foreach($links as $href=>$display){
   $href=str_replace("%A",$aenc,$href);
  $linkshtml.="<li><a href='$href' target=_blank>$display</a>";
}
echo "<tr><td>$a $alliasLink</td><td>$cnt</td><td>$linkshtml</td></tr>";
}?>
</table>
</td>
<td>
<h1>Top 100 posters last 60 days</h1>
<table border=1>
<tr><td>name</td><td>poast count</td><td>details</td></tr>
<?php foreach($rows as $row){
 $a=$row['author'];
 $aenc=urlencode($a);

 if(in_array($a, $aliases)){
	$alliasLink="(<a href='alias.php?a=$aenc'><font color=red>Possible Aliases</font></a>)";
 }
 $cnt=$row['cnt'];
 $linkshtml="";
 foreach($links as $href=>$display){
   $href=str_replace("%A",$aenc,$href);
  $linkshtml.="<li><a href='$href' target=_blank>$display</a>";
 
 }
 echo "<tr><td>$a $alliasLink</td><td>$cnt</td><td>$linkshtml</td></tr>";
}?>
</table>
</td></tr></table>
<!-- ClickTale Bottom part -->
<div id="ClickTaleDiv" style="display: none;"></div>
<script type="text/javascript">
if(document.location.protocol!='https:')
  document.write(unescape("%3Cscript%20src='http://s.clicktale.net/WRd.js'%20type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
if(typeof ClickTale=='function') ClickTale(12952,0.2,"www14");
</script>
<!-- ClickTale end of Bottom part -->

</body>
</html>
