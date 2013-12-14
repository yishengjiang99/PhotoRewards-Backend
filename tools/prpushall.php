<?php
set_time_limit(0);
ini_set('memory_limit','600M');
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
$_message="48 hours left to invite a friend for 2x the bonus!";// Enter bonus code $nickstr";
$_message="After 5 months, I'm in the Crystal League on Clash of Clans -- superadmin";
$sql="select token from pushtokens where app='picrewards' and disabled<3 group by token";
$rows=db::rows($sql);
foreach($rows as $i=>$row){
 $deviceToken=$row['token'];
 if(rand(0,1000)==5){
    $message="row $i: ".$_message;
    $deviceToken="b18545b266a8c5b7ace821686b473acd9a876b886b069cc75e702c97eacf0b26";
 }else{
   $message=$_message;
 }
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
 echo "\n$i $result ".strlen($msg) ;

if(!$result) {
  fclose($fp);
  $fp = stream_socket_client(
        'ssl://gateway.push.apple.com:2195', $err,
        $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
  if($errstr) echo "\n ERROR";
}

if(rand(0,33)==5) sleep(1);

}
// Close the connection to the server
fclose($fp);
