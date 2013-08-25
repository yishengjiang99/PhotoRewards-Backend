<?
require_once("db.class.php");
$start=2200000;
$end  =13393610;
$tid=db::cols("select tid from thread where tid<$end and tid>$start");
$tid=array_values($tid);
for($i=$end;$i>$start;$i--){
 if(in_array($i,$tid)) { echo "\n did $i continue"; continue;}
  echo "\nphp p2.php $i";
   if(rand(0,20)==11) exec("php p2.php $i");
    else  exec("php p2.php $i >> /dev/null 2>&1 &");
    }
    
    
    
