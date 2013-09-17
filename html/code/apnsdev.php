<?php

require_once("/var/www/lib/functions.php");

error_reporting(E_ALL); ini_set('display_errors', '1');
apnsUser(2902,"dddd");
function apnsUser($uid,$badge,$iam="",$url=""){

$token=db::row("select a.token from pushtokens a join appuser b on a.mac_address=b.mac where b.id=$uid and a.app='picrewards' order by a.id desc limit 1");
$tokenstr=$token['token'];
$deviceToken=$tokenstr;
$passphrase='prpr';
// Put your alert message here:

////////////////////////////////////////////////////////////////////////////////

$ctx = stream_context_create();
stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/tools/PRProdCertKey.pem');
stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
// Open a connection to the APNS server
$fp = stream_socket_client(
	'ssl://gateway.push.apple.com:2195', $err,
	$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

if (!$fp)
	exit("Failed to connect: $err $errstr" . PHP_EOL);



// Create the payload body
$body['aps'] = array(
	'alert' => $badge,
	'sound' => 'default',
	'custom_key1'=>'hi',
//  	'msg'=>$iam,
 //	'url'=>"http://www.json9.com/m.php",
	);
if($iam!=""){
 $body['aps']['msg']=$iam;
 if($url!=""){
   $body['aps']['url']=$url;
 }
}

// Encode the payload as JSON
$payload = json_encode($body);

// Build the binary notification
$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

// Send it to the server
$result = fwrite($fp, $msg, strlen($msg));

// Close the connection to the server
fclose($fp);
}
