<?php
require_once("/var/www/lib/functions.php");

$points=db::rows("select sum(vote) as sum, pid from contest_votes group by pid");
foreach($points as $p){
  $pid=$p['pid'];
  $sum=$p['sum'];
  db::exec("update contest_entry set points=$sum where pid='$pid'");
}
