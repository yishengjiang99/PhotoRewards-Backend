<?php

$exclude=array("calm",".,.,...,.,,..,:,,:,..;,:::,..,.,:,.,:....:.,:.::");

$a=$_POST['q'];

$exclude=array("calm",".,.,...,.,,..,:,,:,..;,:::,..,.,:,.,:....:.,:.::");
if(in_array($a,$exclude)){
 die('poast requested to be unsearchable');
}
header("location: /xo2/threads.php?a=".urlencode($a));
exit;
