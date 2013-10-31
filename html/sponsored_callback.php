<?php
// $prcb="https://json999.com/sponsored_callback.php?mac=$mac&idfa=$idfa&storeID=$appid";

require_once("/var/www/lib/functions.php");
if($_GET['pw']=="dafhfadsfkdsadlds") $network='Yisheng';
else if($_GET['pw']=="dafhfadst444") $network='yunan';
else die(0);
$mac=$_GET['mac'];
$idfa=$_GET['idfa'];
$user=db::row("select * from appuser where idfa='$idfa' and app='picrewards' order by id asc limit 1");
//if(!$user) $user=db::row("select * from appuser where mac='$mac' and app='picrewards'");
if(!$user) die("0");

$uid=$user['id'];
$appid=intval($_GET['storeID']);
$offer=db::row("select payout,cash_value,id,active from offers where storeID=$appid");
$points=intval($offer['cash_value'])*10;
if($offer['active']==0) exit;
$offerId=$offer['id'];
$subid=$uid."_".$offerId;
$rev=$offer['payout'];
$token=db::row("select a.token from pushtokens a join appuser b on a.mac_address=b.mac where b.id=$uid and a.app='picrewards'");
$tokenstr=$token['token'];
$transactionID=$user['id'];
db::exec("insert ignore into sponsored_app_installs set uid=$uid, Amount=$points,transactionID='$transactionID', appid=$appid, created=now(),offer_id=$offerId, subid='$subid',network='$network',revenue=$rev");
$installid=db::lastID();
$appstr=file_get_contents("http://json999.com/appmeta.php?appid=$appid");
$app=json_decode($appstr,1);
$appname=$app['Name'];
error_log($appname);
$message="Thanks for trying $appname! Share a screenshot on Picture Rewards!";
require_once("/var/www/html/pr/apns.php");
apnsUser($uid,$message,"");
exit;
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

echo 'Connected to APNS' . PHP_EOL;

// Create the payload body
$body['aps'] = array(
	'alert' => $message,
	'sound' => 'default',
	'custom_key1'=>'hi',
	);

// Encode the payload as JSON
$payload = json_encode($body);

// Build the binary notification
$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

// Send it to the server
$result = fwrite($fp, $msg, strlen($msg));
if (!$result)
	echo 'Message not delivered' . PHP_EOL;
else
	echo 'Message successfully delivered' . PHP_EOL;

// Close the connection to the server
fclose($fp);

