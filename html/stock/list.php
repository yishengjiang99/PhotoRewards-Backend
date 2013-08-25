<?php
$s=array("symbol"=>"NASDAQ","company"=>"google","lastPrice"=>13.32,"high"=>23.23,"low"=>11.11,"change"=>"-11.1","tracking"=>"false");
$s2=array("symbol"=>"goaog","company"=>"google","lastPrice"=>1332,"high"=>2323,"low"=>11,"change"=>"3.2","tracking"=>"true");
$l=array($s,$s2,$s);

header('Content-type: text/json');
header('Content-type: application/json');
echo json_encode($l);
exit;
