
<?php
error_reporting(E_ALL); ini_set('display_errors', '1');

/* Create a new imagick object and read in GIF */
require_once("/var/www/lib/functions.php");
$imgs=db::rows("select id from UploadPictures where reviewed=0 and type='DoneApp'");
foreach($imgs as $img){ 
  $i=$img['id'];
  $file='/var/www/html/pr/uploads/'.$i.'.jpeg';
  $arch="/var/www/html/pr/uploads/arch/".$i.".jpeg";
  if(!file_exists($file) && !file_exists($arch)){
        echo "update UploadPictures set reviewed=-2 where id='$i' limit 1\n";
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
