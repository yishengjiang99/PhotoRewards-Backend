<?
require_once('db.class.php');
$tid=$argv[1];
date_default_timezone_set('America/Los_Angeles');
$url="http://www.xoxohth.com/thread.php?thread_id=$tid&forum_id=2";
/*
          <p><b>Date:</b>  December 13th, 2012 1:50 AM<br><b>Author:</b>  Esq. (<I><A HREF="mailto:=)">=)</A></I>)<br><br>box, gtfih<br><br>
        <font size=1>(http://www.autoadmit.com/thread.php?thread_id=2136585&forum_id=2#22240541)</font></td></tr></table><P><br>
*/

$p="/Date:<\/b>(.*?)<br><b>Author:<\/b>(.*?)<br><br>(.*?)<br><br>/";
$link="/thread_id=(\d+)&forum_id=2#(\d+)/";
$ch=curl_init($url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
$ret=curl_exec($ch);
preg_match_all($p,$ret,$m);
preg_match_all($link,$ret,$ml);
foreach($m[2] as $i=>$author){
$date=date('Y-m-d H:i:s',strtotime(trim($m[1][$i])));
$pid=$ml[2][$i];
$content=$m[3][$i];
$author= trim(preg_replace("/\(<I>(.*?)<\/I>\)/","",$author));
$sql="insert into post set tid=$tid, pid=$pid,author='$author',date='$date',content='$content'";
echo "\n$sql";
}

