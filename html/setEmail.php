<?php
require_once("/var/www/lib/functions.php");
require_once("/var/www/lib/firewall.php");
$uid=intval($_REQUEST['uid']);
$email=$_REQUEST['email'];
db::exec("update appuser set email='$email' where id=$uid");
$user=db::row("select * from appuser where id=$uid");
$username=$user['username'];
die(json_encode(array("title"=>"Email confirmed","msg"=>"Contest prize gift card will be delivered to $email\nYou can ask your friends to vote for your picture by searching for the username '$username' in the search bar")));
