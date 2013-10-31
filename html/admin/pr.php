<?php 
require_once("/var/www/lib/functions.php");
$rid=intval($_GET['id']);
$cmd=$_GET['cmd'];
if($rid!=0){
 if($cmd=='off'){
  db::exec("update PictureRequest set status=0,max_cap=uploadCount+10 where id=$rid");
 }else{ 
  db::exec("update PictureRequest set status=3,max_cap=uploadCount+10 where id=$rid");
 }
 $pic=db::rows("select * from PictureRequest where id=$rid");
 echo rows2table($pic);
 exit;
}

if($_GET['cmd']=='running'){
$rows=db::rows("select concat('/admin/pr.php?cmd=off&id=',a.id) as linkoff, b.ltv, a.created,max_cap, stars,title,cash_bid,gender,uploadCount,username,modified 
from PictureRequest a join appuser b on a.uid=b.id left join fbusers f on b.fbid=f.fbid where a.title!='(null)' and status=3 order by modified limit 100");
}else{
$rows=db::rows("select concat('/admin/pr.php?id=',a.id) as linkrun, b.ltv, a.created, stars,max_cap,title,category,cash_bid,gender,uploadCount,username,modified 
from PictureRequest a join appuser b on a.uid=b.id left join fbusers f on b.fbid=f.fbid where b.stars>0 and a.title!='(null)' and status<3 and ltv>0 and stars<1000 group by b.id order by b.fbid=0 desc, modified desc limit 1000");
 }

echo "<a href='/admin/pr.php?cmd=running'>running</a><br>";
echo "<a href='/admin/pr.php'>queue</a>";

echo rows2table($rows);
