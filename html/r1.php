<?php
$url="http://panel.gwallet.com/network-node/impression?appId=f31d13dd928148729069da21b1d9f7eb&userId=2902&CDF4008D-BA7D-44B1-B623-3013091995AD&format=json&sdk=2.0";

$data=json_decode(file_get_contents($url),1);
var_dump($data);

