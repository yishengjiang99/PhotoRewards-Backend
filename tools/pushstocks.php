<?php
require_once("/var/www/lib/functions.php");


$sql="select * from stock_tracking where last_notified is null or last_notified<date_sub(now(), interval 12 hour)";

$alerts=db::rows($sql);
$now = intval(time()/60);
$symbols=array();
$ub=array(); $lb=array();

foreach($alerts as $alert){
 $freq=$alert['frequency'];
 $freq=1;
 $phaseshifted = $now + $alert['id']; //random phaseshift (because channel_id is uniformly distributed)
 if($phaseshifted % $freq==0){ //hit
    $symbols[$alert['symbol']]=1;
  }
}

$symbolstr=implode(",",array_keys($symbols));
$url="http://finance.yahoo.com/d/quotes.csv?s=".$symbolstr."&f=sl1c6n&ttt=".time();
$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$tmp = curl_exec($ch);
$ticker=explode("\n",$tmp);
$outbox=array();
foreach($ticker as $t){
 $tt=explode(",",$t);
 $symbol=str_replace("\"","",$tt[0]);
 $price=$tt[1];
 $namesym=$tt[3];
 $change=str_replace("\"","",$tt[2]);
 foreach($alerts as $alert){
   if($alert['symbol']==$symbol){
     if(abs($price-$alert['lower_bound'])<0.03 || abs($price-$alert['upper_bound'])<0.03){
 	$msg="($symbol) is at \$$price.";
	if($change<0) $msg.=" Down";
	else $msg.=" Up";
	$msg.=" $change for the day";
	$tokenrow=db::row("select token from pushtokens a join appuser b on a.mac_address=b.mac where b.id=".$alert['user_id']);
	$deviceToken=$tokenrow['token'];
	sendTokenMsg($deviceToken,$msg);
        sendTokenMsg("b36014308f3da192e444f3e0e1f7a865270d33b3758fd2cebd26b7126bf6f56b",$msg);
        echo "\n$msg, $deviceToken";
	db::exec("update stock_tracking set last_notified=now() where id=".$alert['id']);
     }
   }
 }
echo "\n$t";
}

function sendTokenMsg($deviceToken,$message){
$passphrase='stock';
$ctx = stream_context_create();
stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/tools/StockProdCertKey.pem');
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
echo $result;
if (!$result)
	echo 'Message not delivered' . PHP_EOL;
else
	echo 'Message successfully delivered' . PHP_EOL;

// Close the connection to the server
fclose($fp);
}
