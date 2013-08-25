<?php
$xplevel=array(
0=>0,
5=>1,
20=>2,
50=>3,
2000=>4,
5000=>5,
10000=>6,
60000=>7,
100000000=>8,
100000000000000=>9,
);

$levelminbonus=array(
0=>190,
1=>210,
2=>220,
3=>230,
4=>260,
5=>286,
6=>290,
7=>310,
8=>320,
);

$levelmax=array(
0=>350,
1=>570,
2=>650,
3=>1999,
4=>2000,
5=>3000,
6=>4000,
7=>5000, 
8=>6000,
);
$multi=2;
function getBonusPoints($myxp){
 global $xplevel, $levelminbonus, $levelmax,$multi;

 $mylevel=0;
 $nexttlevel=0;
 $nextmin=0;$nextmax=0;
 $lastlevelxp=0;
 $tnl_percent=0;
 foreach($xplevel as $lxp=>$level){
   if($lxp>$myxp){
	$nextlevel=$level;
        $tnl=(double)($myxp-$lastlevelxp);
        $levelxptotal=(double)($lxp-$lastlevelxp);
	$tnl_percent=$tnl/$levelxptotal*100;
        $nextmin=$levelminbonus[$nextlevel]*$multi;
	$nextmax=$levelmax[$nextlevel]*$multi;
        break;
   }
   
   $mylevel=$level;
   $lastlevelxp=$lxp;
 }

 return array('level'=>$mylevel,'xp'=>$myxp,'minbonus'=>$levelminbonus[$mylevel]*$multi,'maxbonus'=>$levelmax[$mylevel]*$multi,'levelPercentage'=>$tnl_percent,'bonusNextLevel'=>"$nextmin to $nextmax Points");
}
