<?php
    $target_path = "/var/www/html/pr/uploads/";
    $target_path = $target_path . basename( $_FILES['userfile']['name']);  
      if(move_uploaded_file($_FILES['userfile']['tmp_name'], $target_path)) {
        echo "http://www.json999.com/pr/uploads/".basename( $_FILES['userfile']['name']);
      } else{
        echo 0;
     }
die();


