<?php
require_once("/var/www/lib/functions.php");
header('Content-Type: application/json');
$tt=explode("_",$_GET['offerId']);
$cid=intval($tt[0]);
$storeId=$tt[1];
$ret=array();
 $sql="select a.id, type, a.uid, b.contest_id,b.points,username, compressed, fbid from UploadPictures a join contest_entry b on a.id=b.pid join appuser c on b.uid=c.id where b.contest_id=$cid order by b.points desc limit 10";
 error_log($sql);
 $pictures=db::rows($sql);
 $photos=array();
 foreach($pictures as $i=>$pic){
       $dir="";
       if($pic['compressed']==5){
         $dir="arch/";
       }
       $filepath="/var/www/html/pr/uploads/".$dir.$pic['id'] . ".jpeg";
       if(!file_exists($filepath)){
          db::exec("update UploadPictures set reviewed=-1 where id=".$pic['id']);
          continue;
        }
	if(!$pic['username']) continue;
	$picture="https://graph.facebook.com/".$pic['fbid']."/picture?width=100&height=100";
	$points=$pic['points'];
        $uidpid="uidpid_".$pic['uid']."_".$pic['contest_id']."_".$storeId;
        $ret[]=array("mainText"=>"#".($i+1).": ".$pic['username'],"offerId"=>$uidpid,"details"=>$pic['points']." total stars from votes",
	"id"=>$pic['id'], "entryLabel"=>"From ".$pic['username'],"uid"=>$pic['uid'], "fbid"=>$pic['fbid'],
	"imgUrl"=>$picture,
        "title"=>$pic['title'],'liked'=>$pic['liked'],'points_earned'=>(int)$pic['points_earned'],'url'=>'http://www.json999.com/pr/uploads/'.$dir.$pic['id'] . '.jpeg?t=1&uid='.$uid);
 }
die(json_encode($ret));
