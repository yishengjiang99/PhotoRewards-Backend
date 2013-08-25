<?php

function poisson( $lambda=1.0 )
{
    static $oldLambda ;
    static $g, $sq, $alxm ;

    if ($lambda <= 12.0) {
        //  Use direct method
        if ($lambda <> $oldLambda) {
            $oldLambda = $lambda ;
            $g = exp(-$lambda) ;
        }
        $x = -1 ;
        $t = 1.0 ;
        do {
            ++$x ;
            $t *= random_0_1() ;
        } while ($t > $g) ;
    } else {
        //  Use rejection method
        if ($lambda <> $oldLambda) {
            $oldLambda = $lambda ;
            $sq = sqrt(2.0 * $lambda) ;
            $alxm = log($lambda) ;
            $g = $lambda * $alxm - gammaln($lambda + 1.0) ;
        }
        do {
            do {
                //  $y is a deviate from a Lorentzian comparison function
                $y = tan(pi() * random_0_1()) ;
                $x = $sq * $y + $lambda ;
            //  Reject if close to zero probability
            } while ($x < 0.0) ;
            $x = floor($x) ;
            //  Ratio of the desired distribution to the comparison function
            //  We accept or reject by comparing it to another uniform deviate
            //  The factor 0.9 is used so that $t never exceeds 1
            $t = 0.9 * (1.0 + $y * $y) * exp($x * $alxm - gammaln($x + 1.0) - $g) ;
        } while (random_0_1() > $t) ;
    }

    return $x ;
}   
function gammaln($in)
{
    $tmp = $in + 4.5 ;
    $tmp -= ($in - 0.5) * log($tmp) ;

    $ser = 1.000000000190015
            + (76.18009172947146 / $in)
            - (86.50532032941677 / ($in + 1.0))
            + (24.01409824083091 / ($in + 2.0))
            - (1.231739572450155 / ($in + 3.0))
            + (0.1208650973866179e-2 / ($in + 4.0))
            - (0.5395239384953e-5 / ($in + 5.0)) ;

    return (log(2.5066282746310005 * $ser) - $tmp) ;
}  
function random_0_1()
{
    //  returns random number using mt_rand() with a flat distribution from 0 to 1 inclusive
    //
    return (float) mt_rand() / (float) mt_getrandmax() ;
}   //  random_0_1()

