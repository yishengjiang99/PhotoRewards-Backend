
<?php
error_reporting(E_ALL); ini_set('display_errors', '1');

/* Create a new imagick object and read in GIF */
require_once("/var/www/lib/functions.php");
$imgs=db::rows("select id from UploadPictures where created>date_sub(now(), interval 20 day) order by created desc limit 50000");
foreach($imgs as $img){ 
  $i=$img['id'];
  $file='/var/www/html/pr/uploads/'.$i.'.jpeg';
  if(!file_exists($file)){
    db::exec("update UploadPictures set reviewed=-2 where id='$i' limit 1");
	continue;
  }
continue;
  $ft='/var/www/html/pr/uploads/m/'.$i.'.jpeg'; 
  try{
   echo "\ncompressing $file";
    $im = new Imagick($file);
    $im->thumbnailImage(640,0);
    $im->writeImage($ft);
      db::exec("update UploadPictures set compressed=2 where id='$i'");
   }catch(Exception $e){
     echo "\n".json_encode($e);
     db::exec("update UploadPictures set reviewed=-1 where id='$i' limit 1");
  }
}
