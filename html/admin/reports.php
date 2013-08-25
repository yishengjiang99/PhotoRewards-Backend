<?php
require_once("/var/www/lib/functions.php");
$revenue_today=db::rows("select sum(amount), network from offer_completions where created>date_sub(now(), interval 1 day group by network");
$newusers_today=db::rows("select count(1) as cnt, app from appuser where created>date_sub(now(), interval 1 day) group by app");

$users=db::rows("select appname,count(1) from appusers group by appname having count(1)>5");

var_dump($revenue_today);
var_dump($users);
