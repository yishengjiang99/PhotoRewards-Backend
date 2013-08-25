<?php

$ctx = stream_context_create();
stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/tools/PRProdCertKey.pem');
stream_context_set_option($ctx, 'ssl', 'passphrase', 'prpr');

// Open a connection to the APNS server
$fp = stream_socket_client(
	'ssl://gateway.push.apple.com:2195', $err,
	$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

if (!$fp) exit("Failed to connect: $err $errstr" . PHP_EOL);

require_once('/var/www/html/pr/apns.php');
require_once('/var/www/lib/functions.php');

$winner=db::row("select sum(Amount) as tot, uid from sponsored_app_installs where created>date_sub(now(), interval 1 day) group by uid order by sum(Amount) desc limit 1");
$winuid=$winner['uid'];

$tot=$winner['tot'];
$winnick=db::row("select username from appuser where id=$winuid");
$nickstr=$winnick['username'];
$message="Winner of the day is $nickstr with $tot Points. Enter bonus code $nickstr";
$sql="select token from pushtokens where app='picrewards' group by token";
$rows=db::rows($sql);
$iam='';
foreach($rows as $row){
 $deviceToken=$row['token'];
 if($deviceToken=='') continue;
 $body['aps'] = array(
	'alert' => $message,
	'sound' => 'default',
	'custom_key1'=>'Ok',
	);
 $payload = json_encode($body);

 // Build the binary notification
 $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

 $result = fwrite($fp, $msg, strlen($msg));
 echo "\n$deviceToken $result";

if(!$fp || $result==0 || $result=='0') {
 echo "remaking";
 $ctx = stream_context_create();
 stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/tools/PRProdCertKey.pem');
 stream_context_set_option($ctx, 'ssl', 'passphrase', 'prpr'); 
  $fp = stream_socket_client(
        'ssl://gateway.push.apple.com:2195', $err,
        $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
 if($errstr) echo "\n ERROR";
}

if(rand(0,33)<5) sleep(1);

}
// Close the connection to the server
fclose($fp);
