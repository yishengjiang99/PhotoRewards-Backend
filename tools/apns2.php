<?php

require_once("/var/www/lib/functions.php");
require_once("/var/www/html/pr/apns.php");

$tt=db::rows("select * from pushtokens where app='picrewards' and modified<date_sub(now(), interval 2 day)");
/*
$tt=db::rows("select a.amount,c.token,b.username,p.name,p.id from sponsored_app_installs a join apps p on a.appid=p.id join appuser b 
on a.uid=b.id join pushtokens c on b.mac=c.mac_address  where a.created>date_sub(now(), interval 2 day) and username!='yisheng' and uploaded_picture=0 and network!='virool' and network!='' 
and a.created>date_sub(now(), interval 2 day) and b.stars>100 and b.stars<200 group by b.id");
$tt=db::rows("select a.* from pushtokens a join appuser b on a.mac_address=b.mac where b.modified<date_sub(now(), interval 2 day) and a.app='picrewards' group by b.id");
*/
$tt=db::rows("select a.* from pushtokens a join appuser b on a.mac_address=b.mac where b.stars>10 and a.app='picrewards' group by b.id");
$tt=db::rows("select * from appuser where app='picrewards'");
$url='';
$iam="";
$tt=db::rows("select reminded, a.amount,c.token,b.username,b.id as uid, p.name,p.id,a.id as installid from sponsored_app_installs a join apps p on a.appid=p.id join appuser b
on a.uid=b.id join pushtokens c on b.mac=c.mac_address  where a.created>date_sub(now(), interval 10 day) and username!='yisheng' and uploaded_picture=0 and network!='virool' and network!=''
and a.created>date_sub(now(), interval 10 day) and b.stars<100 group by b.id");
$tt=db::rows("select id from appuser where app='picrewards' and modified<date_sub(now(), interval 4 day) and stars<1000");
$passphrase='prpr';
$ctx = stream_context_create();
stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/tools/PRProdCertKey.pem');
stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
// Open a connection to the APNS server
$fp = stream_socket_client(
        'ssl://gateway.push.apple.com:2195', $err,
        $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

if (!$fp)
	exit("Failed to connect: $err $errstr" . PHP_EOL);



foreach($tt as $t){
$uid=$t['id'];
 $token=db::row("select a.token from pushtokens a join appuser b on a.mac_address=b.mac where b.id=$uid and a.app='picrewards' order by a.id desc limit 1");
$tokenstr=$token['token'];
$deviceToken=$tokenstr;

 $message="New! $10 Facebook Game Cards. 10% off while supllies last";
$message="Refer-a-friend bonus codes are back! With More Points";
$message="New! Share a screenshot of Game of War - Fire Age";
$message='Post a picture of your dinner for 10 Points (need dinner ideas)';
$message='Post a picture of your dinner for 10 Points (need dinner ideas)';
$message="$5 Starbucks Giftcards for only 2500 Points. 50% off while supplies last";
// Create the payload body

$body['aps'] = array(
        'alert' => $message,
        'sound' => 'default',
        'custom_key1'=>'hi',
        );

echo $message."\n";
// Encode the payload as JSON
$payload = json_encode($body);

// Build the binary notification
$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

// Send it to the server
$result = fwrite($fp, $msg, strlen($msg));
}
