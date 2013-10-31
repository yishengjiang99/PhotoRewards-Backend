<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');
require_once("/var/www/lib/functions.php");

$transit2=array();
if(isset($_POST) && !isset($_POST['cmd'])){
 foreach($_POST as $id=>$reviewed){
   $status = $reviewed=="on" ? 1 : -1;
   $sql="update UploadPictures set reviewed=$status where id='$id' limit 1";
   $transit2[]=$id;
   db::exec($sql);
 }
}
$msg="";
if($_POST['cmd']=="reset"){
 $transit2file=file_get_contents("/var/www/cache/transit2.txt");
 $rc=json_decode($transit2file,1);

 foreach($rc as $id){
   db::exec("update UploadPictures set reviewed=0 where id='$id'");
 } 
 $msg=count($rc)." recently approved pictures reverted";
}
$status=0;
$left=db::row("select count(1) as cnt from UploadPictures a join PictureRequest b on a.refId=b.id where b.status=3 and type='UserOffers' and reviewed=0");
$leftstr=$left['cnt'];

$break=db::row("select count(1) as reviewed from UploadPictures where created>='2013-09-23 04:00:00' and type='DoneApp' and reviewed!=0");
$pics = db::rows("select a.id,a.compressed,b.title as name from UploadPictures a join PictureRequest b on a.refId=b.id where b.status=3 and type='UserOffers' and reviewed=0 order by b.id limit 60");

foreach($pics as $p){
  $id=$p['id'];
   $sql="update UploadPictures set reviewed=-1 where id='$id'";
   $transit2[]=$id;
   db::exec($sql);
error_log($sql);
}
file_put_contents("/var/www/cache/transit2.txt",json_encode($transit2));
?>
<html>
<body>
<?php echo $leftstr; ?> left to do<br>

<?php
echo $msg;
echo  "<br>photos reviewed since 2013-09-22 9pm pacific: ".$break['reviewed'];
echo "<form method=POST><input type=hidden name=cmd value=reset /><input type=submit value='revert last page' /></form>";
?>
<form method=POST>
 <input type=hidden name=start value='<?= $start ?>' />
<input type=hidden name='allids' value='<? echo json_encode($pics) ?>'>
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
<br><br><input type=submit value="APPROVE ALL CHECKED">
</form>
