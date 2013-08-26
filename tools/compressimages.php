
<?php
error_reporting(E_ALL); ini_set('display_errors', '1');

/* Create a new imagick object and read in GIF */
require_once("/var/www/lib/functions.php");
$imgs=db::rows("select id from UploadPictures where compressed<2 and reviewed>=0 and created>date_sub(now(), interval 2 day) order by created desc limit 500");
foreach($imgs as $img){ 
  $i=$img['id'];
  $file='/var/www/html/pr/uploads/'.$i.'.jpeg';
  if(!file_exists($file)){
    echo "\nupdate UploadPictures set reviewed=-1 where id='$i' limit 1";
    db::exec("update UploadPictures set reviewed=-1 where id='$i' limit 1");
	continue;
  }
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
