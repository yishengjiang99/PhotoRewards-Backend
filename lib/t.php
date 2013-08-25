<?
require_once('functions.php');
$f=fopen("posts.txt","r");
while($l=fgets($f)){
 $l=trim($l);
 $t=explode("\t",$l);
 if(!isset($t[4])) continue;

 $pid=$t[0];$tid=$t[1];$author=$t[2];$date=$t[3];$content=$t[4];
 $js='{"index":{"_index":"xo","_type":"posts","_id":"'.$pid.'"}}'."\n";
 $js.=json_encode(array("tid"=>$tid,"author"=>$author,"date"=>$date,"content"=>$content))."\n";
 file_put_contents("posts.bin.".rand(0,20),$js,FILE_APPEND);
}

