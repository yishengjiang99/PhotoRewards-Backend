<?php 
require_once("/var/www/lib/functions.php"); 
$topicId=-1;
$uid=intval($_GET['uid']);

if(strpos($_GET['topicId'],"uidpid")!==false){
 $t=explode("_",$_GET['topicId']);
 $topicId=$t[2];
 $uid=$t[1];
 $contests=db::rows("select r.img as rimg, a.id as offerId, o.click_url, o.name,o.storeID,r.name as reward from contest a join offers o on a.offer_id=o.id join rewards r on a.reward_id=r.id
 order by a.id=$topicId desc limit 2");
 $contest=$contests[0];
 $name=$contest['name'];
 $sql="select shared, title, a.id, type, a.uid, points_earned,username, compressed, fbid from UploadPictures a join contest_entry b on a.id=b.pid join appuser c on b.uid=c.id where b.uid=$uid
 order by b.contest_id=$topicId desc limit 10";
 error_log($sql);
}
if($_GET['pid']=="searchbar"){
 $username=$_GET['topicId'];
 $searchedUser=db::row("select * from appuser where username='$username'");
 if(!$searchedUser){
	   $topicId=-1;
 }else{
  $searchedUid=$searchedUser['id'];
  $topicEntry=db::row("select a.* from contest_entry a join contest b on a.contest_id=b.id where uid=$searchedUid order by b.status=1 desc limit 1");
  $topicId=$topicEntry['contest_id'];
  $contests=db::rows("select r.img as rimg, a.id as offerId, o.click_url, o.name,o.storeID,r.name as reward from contest a join offers o on a.offer_id=o.id join rewards r on a.reward_id=r.id
   order by a.id=$topicId desc limit 2");
  $contest=$contests[0];
  $name=$contest['name'];
  $storeId=$contest['storeID'];
  $sql="select shared, title, a.id, type, a.uid, points_earned,username, compressed, fbid from UploadPictures a join contest_entry b on a.id=b.pid join appuser c on b.uid=c.id where b.uid=$searchedUid order by RAND() limit 10";
  error_log($sql);
 }
}
if(strpos($_GET['topicId'],"la")!==false){
 $t=explode("_",$_GET['topicId']);
 $contestID=$t[1];
 $contests=db::rows("select r.img as rimg, a.id as offerId, o.click_url, o.name,o.storeID,r.name as reward from contest a join offers o on a.offer_id=o.id join rewards r on a.reward_id=r.id order by a.id=$contestID desc limit 1");
 $contest=$contests[0];
 $name=$contest['name'];
 $user=db::row("select * from appuser where id=$uid");
 require_once("/var/www/lib/ssa.php");
 $ssa=getSSAFeed($user,1);
 $offer=$ssa[0];
 $contest['name']=$offer['Name'];
 $contest['storeID']=$offer['StoreID'];
 $contest['offerId']=$offer['StoreID'];
 $topicId=$offer['StoreID'];
 $contest['click_url']=$offer['RedirectURL'];
 $sql="select shared, title, a.id, type, a.uid, points_earned,username, compressed, fbid from UploadPictures a join contest_entry b on a.id=b.pid join appuser c on b.uid=c.id where b. order by RAND() limit 10";
}

if($topicId<0)
{
 $topicId=intval($_GET['topicId']);
 $contests=db::rows("select r.img as rimg, a.id as offerId, o.click_url, o.name,o.storeID,r.name as reward from contest a join offers o on a.offer_id=o.id join rewards r on a.reward_id=r.id
 order by a.id=$topicId desc limit 2");
 $contest=$contests[0];
 $name=$contest['name'];
 $storeId=$contest['storeID'];
 $sql="select shared, title, a.id, type, a.uid, points_earned,username, compressed, fbid from UploadPictures a join contest_entry b on a.id=b.pid join appuser c on b.uid=c.id where b.contest_id=$topicId order by RAND() limit 10";
 error_log($sql);
}

 $name=$contest['name'];
 $storeId=$contest['storeID'];
 $reward=$contest['reward'];
 $url=$contest['click_url']; 
 $offerId=$contest['offerId'];
 $subid=$uid."_".$offerId."_contest".$topicId;
 $url=str_replace("SUBID_HERE",$subid,$url);
 $idfa=$_GET['idfa'];
 $url=str_replace("IDFA_HERE",$idfa,$url);
 $url="http://json999.com/contest/click.php?subid=$subid&go=".urlencode($url);
 if($contests[1]){
  $other=$contests[1];
  $otherTopics=array(array("offerId"=>$other['offerId']));
 }else{
  $otherTopics=array(array("offerId"=>$topicId));
 }
 $rewardUrl=$contest['rimg'];
 $pictures=db::rows($sql);
 $photos=array();
 foreach($pictures as &$pic){
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
        $photos[]=array("id"=>$pic['id'], "entryLabel"=>"From ".$pic['username'],"uid"=>$pic['uid'], "fbid"=>$pic['fbid'],
        "title"=>$pic['title'],'liked'=>$pic['liked'],'points_earned'=>(int)$pic['points_earned'],'url'=>'http://www.json999.com/pr/uploads/'.$dir.$pic['id'] . '.jpeg?t=1&uid='.$uid);
 }

 if(count($photos)<=5){
    $sql="select shared, title, a.id, type, a.uid, points_earned,username, compressed, fbid from UploadPictures a join contest_entry b on a.id=b.pid join appuser c on b.uid=c.id where a.offer_id=$storeId order by RAND() limit 10";
    $pictures=db::rows($sql);
    foreach($pictures as &$pic){
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
        $photos[]=array("id"=>$pic['id'], "entryLabel"=>"From ".$pic['username'],"uid"=>$pic['uid'], "fbid"=>$pic['fbid'],
        "title"=>$pic['title'],'liked'=>$pic['liked'],'points_earned'=>(int)$pic['points_earned'],'url'=>'http://www.json999.com/pr/uploads/'.$dir.$pic['id'] . '.jpeg?t=1&uid='.$uid);
    }
 }
 if(count($photos)<=5){
  $meta=json_decode(file_get_contents("http://json999.com/appmeta.php?appid=$storeId"),1);
  $screenshots=explode(",",$meta['screenshot']);
  foreach($screenshots as $s){
    $photos[]=array("id"=>"appstore","entryLabel"=>$name,'url'=>$s,'appstore'=>"1");
  }   
}
 $info="Hello welcome hello wellcome!!!";
 $ret=array("offerId"=>$topicId,"name"=>$name,"reward"=>$reward,"photos"=>$photos,"url"=>$url,"otherTopics"=>$otherTopics);
 $ret['logoImg']=$rewardUrl;
 $ret['info']="Would you like to take a screenshot of $name and enter the contest to win a $reward";
// $ret['info_popup']="yes";
// $ret['info_url']=$url;
 $ret['joinedContest']="no";
 die(json_encode($ret));
?>

