<?php
require_once("/var/www/lib/functions.php");
$loadmore=1;
$perpage=60;
$msg="";
if(isset($_POST['start']) && !isset($_POST['cmd'])){
 $loadmore=0;
 $intrans=json_decode($_POST['allids']);
 $approved=array();
 foreach($_POST as $id=>$reviewed){
   $status = $reviewed=="on" ? 1 : -1;
   if($reviewed!="on") continue;
   $sql="update UploadPictures set reviewed=$status where id='$id' limit 1";
   db::exec($sql);
   $approved[$id]=1;
 }
 if(isset($_POST['loadmore']) && $_POST['loadmore']=="on"){
    $loadmore=1;
 } 
 $msg.=count($approved)." approved ";
 $notProvedCount=0;
 foreach($intrans as $id){
   $status=1;
   if(!isset($approved[$id])){
     $status=-1;
     $notProvedCount++;    
      $sql="update UploadPictures set reviewed=-1 where id='$id' limit 1";
     db::exec($sql);
   }
   db::exec("insert into PictureMonitor set status=$status, created=now(), pid='$id'");
 }
 $msg.="$notProvedCount not approved"; 
}

$status=0;
$left=db::row("select count(1) as cnt from UploadPictures a join apps b on a.offer_id=b.id where type='DoneApp' and reviewed=0");
$leftstr=$left['cnt'];
$break=db::row("select count(1) as reviewed from UploadPictures where created>='2013-11-07 22:38:41' and type='DoneApp' and reviewed!=0");

if($loadmore){
$pics = db::rows("select a.id, offer_id,a.compressed, b.name from UploadPictures a join apps b on a.offer_id=b.id where type='DoneApp' and reviewed=$status order by b.id limit $perpage");
foreach($pics as $p){
  $id=$p['id'];
  $transit[]=$id;
}
}
?>
<html>
<body>
<?php echo $leftstr; ?> left to do<br>
<?php
echo $msg;
echo "<br>photos reviewed since 2013-09-22 9pm pacific: ".$break['reviewed'];
echo "<br><a href='/admin/revert.php'>Revert recent approved</a><br>";
?>
<?if($loadmore){ ?>
<form method=POST>
<input type=hidden name=start value='<?= $start ?>' />
<input type=hidden name='allids' value='<? echo json_encode($transit) ?>'>
<?php
 $lastone="";
 foreach($pics as $i=>$pic){
 $picid=$pic['id']; 
 $url="http://json999.com/pr/uploads/".$pic['id'].".jpeg";
 if($pic['compressed']==5)  $url="http://json999.com/pr/uploads/arch/".$pic['id'].".jpeg"; 
  $name=$pic['name'];
  if($name!=$lastone){
    echo "<br><br>$name <br>";
  }
  $lastone=$name;
  echo "<input name=$picid type=checkbox CHECKED /><a href=$url target=_blank><img width=100 src=$url></a>";
  if($i % 6 ==5) echo "<br>";
}?>
<br>
<input type=checkbox CHECKED name=loadmore />Load 60 more
<br><input type=submit value="APPROVE ALL CHECKED">
<?php }else{ ?>
<a href="/admin/safety.php">Load 60 more</a>
<?php } ?>

</form>
