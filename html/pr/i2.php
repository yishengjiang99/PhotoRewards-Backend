<?php
require_once("/var/www/lib/functions.php");

$uid=intval($_GET['uid']);
$inbox=db::rows("select a.*,b.fbid,b.username,c.firstname from inbox a join appuser b on a.from_uid = b.id left join fbusers c on b.fbid=c.fbid where to_uid=$uid order by created desc limit 10");
$ret=array();
foreach($inbox as $m){
 $fromname="";

 if($m['firstname']) $fromname=$m['firstname'];
 else $fromname=$m['username'];

 $fromname=$m['username'];
 $box=array("From"=>$fromname,"msg"=>urldecode($m['msg']),"from_uid"=>$m["from_uid"],"msg_id"=>$m['id'],'readmsg'=>$m['readmsg']);
 if($m['fbid']) $box['fbid']=$m['fbid'];
 $ret[]=$box;
}
 $box=array("From"=>"SuperAdmin","msg"=>"Welcome to PhotoRewards! Message me or txt 650-804-6836 if you have any questions!","from_uid"=>2902,"msg_id"=>1,"readmsg"=>1);
 $ret[]=$box;
die(json_encode($ret));
