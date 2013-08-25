<?
require_once("functions.php");
$start=microTime(true);
dbexec("select pid, tid,author,unix_timestamp(date),content from post","posts.txt");
$end=microTime(true);
echo $end-$start;


