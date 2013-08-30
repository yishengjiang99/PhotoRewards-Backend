<?php
require_once("/var/www/lib/functions.php");

if(isset($argv[1])){
 $cmd=explode("432",$argv[1]);
 $uid=intval($cmd[0]);
 $badge=urldecode($cmd[1]);
 $iam=urldecode($cmd[2]);
 $url=urldecode($cmd[3]);
 _apnshUser($uid,$badge,$iam,$url);
 exit;
}
function apnsUser($uid,$badge,$iam="",$url=""){
 $badge=urlencode($badge);
 $iam=urlencode($iam);
 $url=urlencode($url);
 $cmdstr= "php /var/www/html/pr/apns.php ".$uid."432".$badge."432".$iam."432".$url." > /dev/null 2>&1 &";
 error_log($cmdstr);
 exec($cmdstr);
}

function _apnshUser($uid, $badge, $iam="", $url=""){
 $token=db::row("select a.token from pushtokens a join appuser b on a.idfa=b.idfa where b.id=$uid and a.app='picrewards' order by a.id desc limit 1");
 $tokenstr=$token['token'];
 $deviceToken=$tokenstr;
 error_log("UID $uid token $badge $deviceToken");
 $passphrase='prpr';
////////////////////////////////////////////////////////////////////////////////

$ctx = stream_context_create();
stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/tools/PRProdCertKey.pem');
stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
// Open a connection to the APNS server
$fp = stream_socket_client(
	'ssl://gateway.push.apple.com:2195', $err,
	$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

if (!$fp){
	error_log("Failed to connect: $err $errstr" . PHP_EOL);
         return;
}

// Create the payload body
$body['aps'] = array(
	'alert' => $badge,
	'sound' => 'default',
	'custom_key1'=>'hi',
 	'url'=>$url,
	);

if($iam!=""){
 $body['aps']['msg']=$iam;
}

// Encode the payload as JSON
$payload = json_encode($body);

// Build the binary notification
$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

// Send it to the server
$result = fwrite($fp, $msg, strlen($msg));
error_log($result);
// Close the connection to the server
fclose($fp);
}
?>
