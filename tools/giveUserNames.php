<?php
require_once("/var/www/lib/functions.php");
$users=db::rows("select * from appuser where created>date_sub(now(), interval 6 hour) and username=''");
foreach($users as $u){
 $uid=$u['id'];
    $nickname=db::row("select * from available_nicknames where taken=0 order by RAND() limit 1");
   $uname=$nickname['nickname'];
   db::exec("update available_nicknames set taken=1, uid=$uid where nickname='$uname'");
   db::exec("update appuser set username='$uname' where id=$uid");
   $nickname=$uname;
}

var_dump($users);
