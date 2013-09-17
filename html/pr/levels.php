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
100000=>8,
400000=>9,
);

$levelminbonus=array(
0=>20,
1=>180,
2=>260,
3=>360,
4=>420,
5=>556,
6=>610,
7=>750,
8=>890,
);

$levelmax=array(
0=>50,
1=>200,
2=>470,
3=>1999,
4=>2000,
5=>3000,
6=>4000,
7=>5000, 
8=>6000,
);

$multiplier=1.6;

function getBonusPoints($myxp,$country='US'){
 global $xplevel, $levelminbonus, $levelmax,$multiplier;
 if(!$multiplier) $multiplier=1;
 if($country!='US' && $country!='CA' && $country!='GB') $multiplier=0.5;
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
        $nextmin=$levelminbonus[$nextlevel]*$multiplier;
	$nextmax=$levelmax[$nextlevel]*$multiplier;
        break;
   }
   $mylevel=$level;
   $lastlevelxp=$lxp;
 }

 return array('level'=>$mylevel,'xp'=>$myxp,'minbonus'=>$levelminbonus[$mylevel]*$multiplier,'maxbonus'=>$levelmax[$mylevel]*$multiplier,'levelPercentage'=>$tnl_percent,'bonusNextLevel'=>"$nextmin to $nextmax Points");
}
