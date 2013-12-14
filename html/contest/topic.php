<?php 
require_once("/var/www/lib/functions.php"); 
$uid=intval($_GET['uid']);
$topicId=$_GET['topicId']; 

   $url="http://photorewards.net/superadmin";
if($topicId=="" || $topicId=="la_24"){
 $topic=db::row("select * from contest where status=1 limit 1");
 $topicId="la_".$topic['id'];
}

if($topicId=="crosspr"){
  $photos=array();
  $photos[]=array("id"=>"appstore","entryLabel"=>"Photo Rewards!",'url'=>"http://d1y3yrjny3p2xa.cloudfront.net/PR_banner.png",'appstore'=>"1");
  $photos[]=array("id"=>"appstore","entryLabel"=>"Photo Rewards!",'url'=>"http://d1y3yrjny3p2xa.cloudfront.net/prbanner.jpg",'appstore'=>"1");
  $photos[]=array("id"=>"appstore","entryLabel"=>"Photo Rewards!",'url'=>"http://d1y3yrjny3p2xa.cloudfront.net/1209105_295087157295947_443188279_n.png",'appstore'=>"1");
  $photos[]=array("id"=>"appstore","entryLabel"=>"Photo Rewards!",'url'=>"http://d1y3yrjny3p2xa.cloudfront.net/LandingPage_Phone2_06.png",'appstore'=>"1");
  $photos[]=array("id"=>"appstore","entryLabel"=>"Photo Rewards!",'url'=>"http://i.imgur.com/EApqc4n.png",'appstore'=>"1");
  $photos[]=array("id"=>"appstore","entryLabel"=>"Photo Rewards!",'url'=>"http://d1y3yrjny3p2xa.cloudfront.net/prbanner.jpg",'appstore'=>"1");
  $photos[]=array("id"=>"appstore","entryLabel"=>"Photo Rewards!",'url'=>"http://d1y3yrjny3p2xa.cloudfront.net/1209105_295087157295947_443188279_n.png",'appstore'=>"1");
  $photos[]=array("id"=>"appstore","entryLabel"=>"Photo Rewards!",'url'=>"http://d1y3yrjny3p2xa.cloudfront.net/prbanner.jpg",'appstore'=>"1");
  $otherTopics=array(array("offerId"=>"la_24"));   
  $name="PhotoRewards!";
  $reward="PayPal Cashouts starting at 75 cents!";
   $url="http://photorewards.net/superadmin";
  $ret=array("offerId"=>"crosspr","name"=>$name,"reward"=>$reward,"photos"=>$photos,"url"=>$url,"otherTopics"=>$otherTopics);
  $ret['info']="Share Photos; Earn Free Gift Cards for iTunes, Starbucks, xBox and PayPal Cash!\n\nDownload PhotoRewards Now!";
  $ret['info_popup']="yes";
  
  $ret['info_url']="http://photorewards.net/superadmin"; 
  $ret['joinedContest']="no";
  die(json_encode($ret));
}

if(strpos($topicId,"uidpid")!==false){
 $t=explode("_",$topicId);
 $contestID=$t[2];
 $uid=$t[1]; 
 $topicId=$t[2];
 $storeId=$t[3];
 $contest=db::row("select r.img as rimg, 'uploaded' as name,a.id as offerId,r.name as reward from contest a join rewards r on a.reward_id=r.id order by a.id=$contestID desc limit 1");
 require_once("/var/www/lib/ssa.php");
 $user=db::row("select * from appuser where id=$uid");
 $ssa=getSSAFeed($user,1,0,$storeId);
 $offer=$ssa[0];
 $contest['name']=$offer['Name'];
 $contest['storeID']=$offer['StoreID'];
 $contest['offerId']=$offer['StoreID'];
 $topicId=$contestID."_".$offer['StoreID'];
 $contest['click_url']=$offer['RedirectURL'];
 $name="contest";
 $sql="select shared, title, a.id, type, a.uid, points_earned,username, compressed, fbid from UploadPictures a join contest_entry b on a.id=b.pid join appuser c on b.uid=c.id where b.uid=$uid
 order by b.contest_id=$contestID desc limit 10";
 error_log($sql);
}

if($_GET['pid']=="searchbar"){
 $username=$topicId;
 $searchedUser=db::row("select * from appuser where username='$username'");
 if($searchedUser){
  $searchedUid=$searchedUser['id'];
  $topicEntry=db::row("select a.* from contest_entry a join contest b on a.contest_id=b.id where uid=$searchedUid order by b.status=1 desc limit 1");
  if($topicEntry){
   $topicId=$topicEntry['contest_id'];
   $contests=db::rows("select r.img as rimg, a.id as offerId, o.click_url, o.name,o.storeID,r.name as reward from contest a join offers o on a.offer_id=o.id join rewards r on a.reward_id=r.id
    order by a.id=$topicId desc limit 2");
   $contest=$contests[0];
   $name=$contest['name'];
   $storeId=$contest['storeID'];
   $sql="select shared, title, a.id, type, a.uid, points_earned,username, compressed, fbid from UploadPictures a join contest_entry b on a.id=b.pid join appuser c on b.uid=c.id where b.uid=$searchedUid order by RAND() limit 10";
  }
 }
}

$showpopups=0;
if(strpos($topicId,"la")!==false){
 $t=explode("_",$topicId);
 $contestID=$t[1];
 if(isset($t[2]) && $t[2]=="spu") $showpopups=1;
 $contest=db::row("select r.img as rimg, a.id as offerId,r.name as reward from contest a join rewards r on a.reward_id=r.id order by a.id=$contestID desc limit 1");
 $pending=db::row("select appid, Name from sponsored_app_installs a join apps b on a.appid=b.id where uid=$uid and uploaded_picture=0 limit 1");
 if($pending){
   $contest['name']=$pending['Name'];
   $contest['storeID']=$pending['appid'];
   $contest['offerId']=$pending['appid'];
   $topicId=$contestID."_".$pending['appid'];  
   $popupMsg="Eligibility Confirmed!\n\nPlease upload a screenshot of ".$pending['Name'];
 }else{
  $user=db::row("select * from appuser where id=$uid");
  require_once("/var/www/lib/ssa.php");
  $ssa=getSSAFeed($user,1);
  $offer=$ssa[0];
  $contest['name']=$offer['Name'];
  $contest['storeID']=$offer['StoreID'];
  $contest['offerId']=$offer['StoreID'];
  $topicId=$contestID."_".$offer['StoreID'];
 }
 $contest['click_url']=$offer['RedirectURL'];
 $storeId=$offer['StoreID'];
 $sql="select shared, title, a.id, type, a.uid, points_earned, username, compressed, fbid from UploadPictures a join appuser c on a.uid=c.id where a.offer_id=$storeId and reviewed=1 order by RAND() limit 10";
}


 $name=$contest['name'];
 $storeId=$contest['storeID'];
 $reward=$contest['reward'];
 $rewardUrl=$contest['rimg'];
 $url=$contest['click_url']; 
 $offerId=$contest['offerId'];
 $subid=$uid."_".$offerId."_contest".$topicId;
 $idfa=$_GET['idfa'];
 $url=str_replace("SUBID_HERE",$subid,$url);
 $url=str_replace("IDFA_HERE",$idfa,$url);
 if(isset($contests[1])){
  $other=$contests[1];
  $otherTopics=array(array("offerId"=>$other['offerId']));
 }else{
  $otherTopics=array(array("offerId"=>"la_".$contestID));
 }
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


 if($storeId && count($photos)<=5){
  $sql="select shared, title, a.id, type, a.uid, points_earned,username, compressed, fbid from UploadPictures a join contest_entry b on a.id=b.pid join appuser c on b.uid=c.id where b.storeId=$storeId order 
  by RAND() limit 10";
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
        $photos[]=array("id"=>$pic['id']."_".$contestID, "entryLabel"=>"From ".$pic['username'],"uid"=>$pic['uid'], "fbid"=>$pic['fbid'],
        "title"=>$pic['title'],'liked'=>$pic['liked'],'points_earned'=>(int)$pic['points_earned'],'url'=>'http://www.json999.com/pr/uploads/'.$dir.$pic['id'] . '.jpeg?t=1&uid='.$uid);
    }
 }
 if(count($photos)<=5 && $storeId>0){
  $meta=json_decode(file_get_contents("http://json999.com/appmeta.php?appid=$storeId"),1);
  $screenshots=explode(",",$meta['screenshot']);
  foreach($screenshots as $s){
    $photos[]=array("id"=>"appstore","entryLabel"=>$name,'url'=>$s,'appstore'=>"1");
  }   
}

if($topicId=='crosspr' || count($photos)<=5){
    $photos[]=array("id"=>"appstore","entryLabel"=>"Photo Rewards!",'url'=>"http://i.imgur.com/EApqc4n.png",'appstore'=>"1");
     $photos[]=array("id"=>"appstore","entryLabel"=>"Photo Rewards!",'url'=>"http://i.imgur.com/L2Qs1qh.png",'appstore'=>"1");
  $photos[]=array("id"=>"appstore","entryLabel"=>"Photo Rewards!",'url'=>"http://i.imgur.com/L2Qs1qh.png",'appstore'=>"1");
  $photos[]=array("id"=>"appstore","entryLabel"=>"Photo Rewards!",'url'=>"http://d1y3yrjny3p2xa.cloudfront.net/PR_banner.png",'appstore'=>"1");
  $photos[]=array("id"=>"appstore","entryLabel"=>"Photo Rewards!",'url'=>"http://d1y3yrjny3p2xa.cloudfront.net/prbanner.jpg",'appstore'=>"1");
  $photos[]=array("id"=>"appstore","entryLabel"=>"Photo Rewards!",'url'=>"http://d1y3yrjny3p2xa.cloudfront.net/1209105_295087157295947_443188279_n.png",'appstore'=>"1");
  $photos[]=array("id"=>"appstore","entryLabel"=>"Photo Rewards!",'url'=>"http://d1y3yrjny3p2xa.cloudfront.net/LandingPage_Phone2_06.png",'appstore'=>"1");
  $photos[]=array("id"=>"appstore","entryLabel"=>"Photo Rewards!",'url'=>"http://i.imgur.com/EApqc4n.png",'appstore'=>"1");
  $photos[]=array("id"=>"appstore","entryLabel"=>"Photo Rewards!",'url'=>"http://d1y3yrjny3p2xa.cloudfront.net/prbanner.jpg",'appstore'=>"1");
  $photos[]=array("id"=>"appstore","entryLabel"=>"Photo Rewards!",'url'=>"http://d1y3yrjny3p2xa.cloudfront.net/1209105_295087157295947_443188279_n.png",'appstore'=>"1");
  $photos[]=array("id"=>"appstore","entryLabel"=>"Photo Rewards!",'url'=>"http://d1y3yrjny3p2xa.cloudfront.net/prbanner.jpg",'appstore'=>"1");
}

 $ret=array("offerId"=>$topicId,"name"=>$name,"reward"=>$reward,"photos"=>$photos,"url"=>$url,"otherTopics"=>$otherTopics);
 $ret['logoImg']=$rewardUrl;

if($showpopups){
 if($popupMsg){
   $ret['info']="$name\n\n Click 'Go' to take a screenshot from within the app\n\nEnter the contest to win a $reward";
 }else{
  $ret['info']="$name\n\n Click 'Go' to take a screenshot from within the app\n\nEnter the contest to win a $reward";
  $ret['info_url']=$url;
 }
 $ret['info_popup']="yes";
}
 error_log("contest $url click");
 $ret['joinedContest']="no";
 die(json_encode($ret));
?>

