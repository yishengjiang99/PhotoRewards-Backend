
<?php
error_reporting(E_ALL); ini_set('display_errors', '1');

/* Create a new imagick object and read in GIF */
require_once("/var/www/lib/functions.php");
$imgs=db::rows("select id from UploadPictures where compressed!=5 and created<date_sub(now(), interval 5 day) order by created asc limit 500000");
foreach($imgs as $img){ 
  $i=$img['id'];
  $file='/var/www/html/pr/uploads/'.$i.'.jpeg';
  if(!file_exists($file)){
       db::exec("update UploadPictures set compressed=5 where id='$i' limit 1");
	continue;
  }
  $ft='/var/www/html/pr/uploads/arch/'.$i.'.jpeg'; 
       db::exec("update UploadPictures set compressed=5 where id='$i' limit 1");

  $suc=rename($file,$ft);
  if($suc){
           echo "\nupdate UploadPictures set compressed=5 where id='$i' limit 1";
         db::exec("update UploadPictures set compressed=5 where id='$i' limit 1");
  }else{
       echo "failed move $i\n";
  }
}
