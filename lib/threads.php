<?php
require_once("db.class.php");

$url="http://www.xoxohth.com/main.php?forum_id=2";

$ch=curl_init($url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
$ret=curl_exec($ch);

/*
<FONT size=1 face='MS Sans Serif'><a href='/thread.php?thread_id=2121281&mc=5&forum_id=2&PHPSESSID=5bcd1568108e83a0d80942d2378553f8'>The way Box desperately tries to fit in here is very Doobsian</a></td>
                                <td><FONT size=1 face='MS Sans Serif'>&nbsp;&nbsp;&nbsp;12/13/12 </td>
                                <Td><FONT size=1 face='MS Sans Serif'>&nbsp;(5)</td>
*/

$thread="/thread_id=(\d+)&(.*?)>(.*?)<\/a>/";
preg_match_all($thread,$ret,$m);
$ts=array_combine($m[1],$m[3]);
foreach($ts as $tid=>$title){
  $sql="insert ignore into thread set tid=$tid, title='$title'";
	db::exec($sql);
exec("php p.php $tid");exit;
}

