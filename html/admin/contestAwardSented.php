<?php
require_once("/var/www/lib/functions.php");
$id=intval($_GET['id']);
db::exec("update contest_winner set sentReward=1 where id=$id");
