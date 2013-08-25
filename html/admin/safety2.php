<?php
require_once("/var/www/lib/functions.php");
if(isset($_POST)){
 foreach($_POST as $id=>$reviewed){
   $status = $reviewed=="on" ? 1 : -1;
   $sql="update UploadPictures set reviewed=$status where id='$id'";
   db::exec($sql);
 }
}
$start=0;
$status=0;
if(isset($_REQUEST['start'])){
 $start=$_REQUEST['start'];
}
if(isset($_REQUEST['i'])){
 $status=$_REQUEST['i'];
}

$pics = db::rows("select a.id, offer_id, b.name from UploadPictures a join apps b on a.offer_id=b.id where type='DoneApp' and reviewed=$status order by b.id limit 60");
foreach($pics as $p){
 $id=$p['id'];
    $sql="update UploadPictures set reviewed=-1 where id='$id'";
   db::exec($sql);
}
$start=$start+20;
if(count($pics)==0){

}
?>
<html>
<body>
<li><a href='/admin/safety.php?i=0'>pending</a>
<li><a href='/admin/safety.php?i=1'>approved</a>
<li><a href='/admin/safety.php?i=-1'>rejected</a>

<form method=POST>
 <input type=hidden name=start value='<?= $start ?>' />
<input type=hidden name='allids' value='<? echo json_encode($pics) ?>'>
<?php
$lastone="";
 foreach($pics as $i=>$pic){
 $picid=$pic['id']; 
 $url="http://json999.com/pr/uploads/".$pic['id'].".jpeg";
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
