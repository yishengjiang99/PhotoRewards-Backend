
<?
require_once("/var/www/lib/db.class.php");
$result = db::rows("SHOW FULL PROCESSLIST");
foreach($result as $row){
$process_id=$row["Id"];
if ($row["Time"] > 40 ) {
$sql="KILL $process_id";
echo $sql."\n";
db::exec($sql);
//mysql_query($sql);
}
}



