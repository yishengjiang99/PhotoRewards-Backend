<?php
if($_GET['sync']!=1){
  $url=$_SERVER['SCRIPT_URI']."?".$_SERVER['QUERY_STRING']."&sync=1";
error_log($url);
  exec("curl '$url'  > /dev/null 2>&1 &");
  die("1");
}
