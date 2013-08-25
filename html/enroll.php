<?php
$iPhoneSignedResponse = file_get_contents('php://input');

 


$myFile = "/var/www/configout";

$fh = fopen($myFile, 'a') or die("can't open file");

fwrite($fh, $iPhoneSignedResponse);

fclose($fh);


 

preg_match('/(<dict.*dict>)/msU', $iPhoneSignedResponse, $matches);

$xml = new SimpleXMLElement($matches[1]);

 

$key_idx=0;

$key_cnt=count($xml->key);

while ($key_idx<$key_cnt) {

  if($xml->key[$key_idx]=='UDID') {

     $udid_key_idx=$key_idx;

  }

  if($xml->key[$key_idx]=='PRODUCT') {

     $device_key_idx=$key_idx;

  }

  if($xml->key[$key_idx]=='IMEI') {

     $imei_key_idx=$key_idx;

  }

  if($xml->key[$key_idx]=='ICCID') {

     $iccid_key_idx=$key_idx;

  }

  if($xml->key[$key_idx]=='VERSION') {

     $version_key_idx=$key_idx;

  }

  if($xml->key[$key_idx]=='MAC_ADDRESS_EN0') {

     $mac_key_idx=$key_idx;

  }

  $key_idx++;

}

 

$device = $xml->string[$device_key_idx];

$udid = $xml->string[$udid_key_idx];

$imei = $xml->string[$imei_key_idx];

$iccid = $xml->string[$iccid_key_idx];

 

$version = $xml->string[$version_key_idx];

$mac = $xml->string[$mac_key_idx];

 

header ('HTTP/1.1 301 Moved Permanently');

header ('Location: http://json999.com/show_device_info.php?udid='.$udid.'&device='.$device.'&imei='.$imei.'&iccid='.$iccid.'&version='.$version.'&mac='.$mac);

 


