<?php
require_once('/var/www/lib/functions.php');

for($i=5; $i<7;$i++){
 if($i>2) $locked=1;
 else $locked=0;

 $starcost=6;
 $prompt='Unlock this bonus picture for STAR_COST_HERE stars?';
 $sql="insert into slide_assets set picname='g$i.jpg',locked=$locked,prompt='$prompt',display_order=0,cost=$starcost";
 db::exec($sql);
}
 
