<?php
require_once("/var/www/lib/functions.php");
$p=$_POST;
var_dump($_POST);
if($p['codes']!="" && intval($p['rid'])){
  $rid=intval($p['rid']);
  $ct=explode("\n",trim($p['codes']));
  foreach($ct as $c){
    $c=trim($c);
    $sql="insert ignore into reward_codes set reward_id=$rid, code=AES_ENCRYPT('$c','supersimple'), created=now();";
    echo $sql."<br>";
    db::exec($sql);
  }
}
unset($_POST['rid']);
unset($_POST['codes']);
$rewards=db::rows("select id, concat(name,CashValue) as name from rewards where type='gc'");
$select="<select name='rid'>";
foreach($rewards as $r){
 $select.="<option value=".$r['id'].">".$r['name']."</option>";
}
$select.="</select>";
?>
<html>
<body>
<form method=POST>
reward type<br>
<?echo $select ?>
<br>
codes<br>
<textarea cols=60 rows=30 name='codes'>
</textarea><br>
<input type=submit value=enter />
</form>
