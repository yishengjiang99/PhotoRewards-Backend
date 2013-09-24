<?php
$n=file_get_contents('nouns.txt');
$nt=preg_split('/\s+/', trim($n));

$adv=file_get_contents('adj.txt');
$at=preg_split('/\s+/', trim($adv));

require_once("/var/www/lib/functions.php");
$n=array_cartesian_product(array($nt,$at));

foreach($n as $tt){
 $nick=$tt[1].$tt[0];
echo "\n$nick";
 db::exec("insert ignore into available_nicknames set nickname='$nick'");
}
//var_dump($n);
function array_cartesian_product($arrays)
{
    $result = array();
    $arrays = array_values($arrays);
    $sizeIn = sizeof($arrays);
    $size = $sizeIn > 0 ? 1 : 0;
    foreach ($arrays as $array)
        $size = $size * sizeof($array);
    for ($i = 0; $i < $size; $i ++)
    {
        $result[$i] = array();
        for ($j = 0; $j < $sizeIn; $j ++)
            array_push($result[$i], current($arrays[$j]));
        for ($j = ($sizeIn -1); $j >= 0; $j --)
        {
            if (next($arrays[$j]))
                break;
            elseif (isset ($arrays[$j]))
                reset($arrays[$j]);
        }
    }
    return $result;
}
