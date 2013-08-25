<html>
<body>
<a href="http://t.mobitrk.com/?a=t&aff_id=502&ad_id=2840&o_id=1280">
	<img style="width:768px;height:66px" src="http://panel.clicksmob.com/files/1280/image_banners/ipad-_ie_768x66_en-1.gif">
	<img src="http://t.mobitrk.com/?a=i&aff_id=502&ad_id=2840&o_id=1280" width="1" height="1" />
</a>

<a href="http://www.tkqlhce.com/click-6500692-10277142" target="_top">Don't stand in the line - buy the New York Pass online</a><img src="http://www.lduhtrp.net/image-6500692-10277142" width="1" height="1" border="0"/>
<br>
</body>
</html>
<?
if(strpos($_SERVER['HTTP_USER_AGENT'],"iPhone")!==false){
 // header("location: /start.php");
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
if(!$data || $data['ttl']<time()){
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
<style>
#iff{height:200px}
</style>
</head>
<body>

<div>
<a target="_blank" href="http://merchant.trialpay.com/ref?tp=HKZw6nH"><img border="0" src="http://d261sv3xac0f7i.cloudfront.net/m/affiliatebuttons/MERCHANT_referral_green_202_36.gif" alt="TrialPay Referral Program" /></a>
<li><a href="http://json999.com/xo2/posters_ranked_by_narcissism.html">posters_ranked_by_narcissism (<font color=red>New</font>)</a>

<br>
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
        if(rand(0,3)==1) $alliasLink='<br>(<a href="http://www.tkqlhce.com/click-6500692-10277142"><font color=red>Possible Great Deal<font></a>)';

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
	if(rand(0,3)==1) $alliasLink='(<a href="http://www.tkqlhce.com/click-6500692-10277142"><font color=red>Possible Great Deal<font></a>)';
 }
//echo '<br><a href="http://www.tkqlhce.com/click-6500692-10277142" target="_top">Don\'t stand in the line - buy the New York Pass online</a><img src="http://www.lduhtrp.net/image-6500692-10277142" width="1" height="1" border="0"/>';
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
        if(rand(0,3)==1) $alliasLink='(<a href="http://www.tkqlhce.com/click-6500692-10277142"><font color=red>Possible Great Deal<font></a>)';
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

</body>
</html>
