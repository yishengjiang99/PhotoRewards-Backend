<?php

// Put your device token here (without spaces):

$deviceToken = '0f744707bebcf74f9b7c25d48e3358945f6aa01da5ddb387462c7eaf61bbad78';

$deviceToken="3e532ad03ed311a700e9a6c4c9cd7bf7ea727ad69dc7177c394c2950ce558202";
$deviceToken='b233bb756bba8d68f960f1f1cfa753be4a2490d138765b46e3cf018e406ab3ec';
$deviceToken='b36014308f3da192e444f3e0e1f7a865270d33b3758fd2cebd26b7126bf6f56b';
$deviceToken='ad96d39afccd1a67a5c244e96b1b781d55b0b68344860d9bfe98a046b1824fe5';
$deviceToken='23fd3066edd60c09e096b8a72185b52c08eaba2e4c71cb51c3b4d4b23463c35f';
$passphrase='slide';

// Put your alert message here:


$message = "Your friend Amanda Stolpa's birthday is in 3 days!";
$message="MSFT (Microsoft Corporation) is now at 33.05. Up 1.11111% for the day";
////////////////////////////////////////////////////////////////////////////////

$ctx = stream_context_create();
stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/tools/PRProdCertKey.pem');
stream_context_set_option($ctx, 'ssl', 'passphrase', 'prpr');

// Open a connection to the APNS server
$fp = stream_socket_client(
	'ssl://gateway.push.apple.com:2195', $err,
	$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

if (!$fp) exit("Failed to connect: $err $errstr" . PHP_EOL);
require_once('/var/www/html/pr/apns.php');
$sql="select a.token,xp,username from pushtokens a join appuser b on a.mac_address=b.mac where b.app='picrewards' and stars<1000 and deviceInfo not like '%ipod%' group by token";
$rank=0;
$rows=db::rows($sql);
$iam='';

foreach($rows as $row){
 $deviceToken=$row['token'];
 $message="You gained ".$row['xp']." XP this week. Ranked in the top $rank%. Play now to get a head start on next week's tourament";
$message="All Puzzles are Free (unlocked) for the next 24 hours. DOUBLE XP.";
$message="Register now with Facebook to earn 50 points toward iTunes, Amazon GiftCards or PayPal Cash";
$message="Discover Apps, Upload Pictures, Earn Giftcards with PhotoRewards";
$message="Download PhotoRewards from the AppStore and enter my bonus code 'superadmin' for up to 1999 Points";
$message='Post a picture of your dinner for 10 Points (need dinner ideas)';
$message="$5 Starbucks Giftcards for only 2500 Points. 50% off while supplies last.";
$message="Refer a friend in the next 48 hours and get DOUBLE the Points";
$message="Checkout the screenshot I just uploaded for Game of War - Fire Age";
$message="Refer a friend in the next 48 hours and get DOUBLE the Points";
$message='New! Checkout Hotspot Shield VPN - Best VPN for Wi-Fi Security, Privacy';
$message="Amazon.com Gift Card Codes are back in stock!";
$message="PhotoRewards v1.1 is here! Update from the App Store";
$message="Good morning everyone! - superadmin";
$message="$5 Amazon Gift Card Codes are back in stock. 10% off for limited time!";
$message="New! Try Ceasar Slots and share a screenshot of the app!";
$message="New! Try Kingdoms of Zenia: Dragon Wars and share a screenshot!";
$message="$5 Starbucks Giftcards for only 2500 Points. 50% off while supplies last.";
$message="New! Message 'addfriend' to any user to add them as a friend (30 XP)";
$message="Amazon.com Gift Card Codes are back in stock!";
$message="A few more hours to share a screenshot of Keek for 500 Points";
$message="New! share a screenshot of War of Regions!";
 $body['aps'] = array(
	'alert' => $message,
	'sound' => 'default',
	'custom_key1'=>'Ok',
	);
if($iam!='') $body['aps']['msg']=$iam;
$payload = json_encode($body);

// Build the binary notification
$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
// Send it to the server
$result = fwrite($fp, $msg, strlen($msg));
echo "\n$deviceToken $result";
if(!$fp || $result==0 || $result=='0') {
$ctx = stream_context_create();
stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/tools/PRProdCertKey.pem');
stream_context_set_option($ctx, 'ssl', 'passphrase', 'prpr');
$fp = stream_socket_client(
        'ssl://gateway.push.apple.com:2195', $err,
        $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

}
}
// Close the connection to the server
fclose($fp);
