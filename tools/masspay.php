<?php

$environment = 'live';	// or 'beta-sandbox' or 'live'

$emailSubject =urlencode('You got Paid!');
$receiverType = urlencode('EmailAddress');
$currency = urlencode('USD');							// or other currency ('GBP', 'EUR', 'JPY', 'CAD', 'AUD')


// Add request-specific fields to the request string.
$nvpStr="&EMAILSUBJECT=$emailSubject&RECEIVERTYPE=$receiverType&CURRENCYCODE=$currency";

$receiversArray = array();
require_once("/var/www/lib/functions.php");
$rows=db::rows("select * from PaypalTransactions where status in ('init','failed') and amount<1200");

if(count($rows)==0) exit;
foreach($rows as $i=>$row){
  $id=$row['id'];
  $amount=(double)$row['amount']/100;
  $tid=$row['masspay_trx_id'];
  $email=$row['email'];
/*
if($email=='356duyen@gmail.com') continue;
if($email=='hj.tommm@yahoo.com') continue;
if($email=='linhngoc2907@gmail.com') continue;
if($email=='longthang6@yahoo.com') continue;
*/
  db::exec("update PaypalTransactions set status='processed' where id=$id");
          $receiverData = array(  'receiverEmail' => $row['email'],
                                                        'amount' =>$amount."",
                                                        'uniqueID' =>$tid,
                                                        'note' => "Please rate us in the App Store! http://bit.ly/12DJBU5");
        $receiversArray[$i] = $receiverData;
}

foreach($receiversArray as $i => $receiverData) {
	$receiverEmail = urlencode($receiverData['receiverEmail']);
	$amount = urlencode($receiverData['amount']);
	$uniqueID = urlencode($receiverData['uniqueID']);
	$note = urlencode($receiverData['note']);
	$nvpStr .= "&L_EMAIL$i=$receiverEmail&L_Amt$i=$amount&L_UNIQUEID$i=$uniqueID&L_NOTE$i=$note";
}
// Execute the API operation; see the PPHttpPost function above.
$httpParsedResponseAr = PPHttpPost('MassPay', $nvpStr);

if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
	exit('MassPay Completed Successfully: '.print_r($httpParsedResponseAr, true));
} else  {
  email("yisheng@grepawk.com","Mass pay failed!!",json_encode($httpParsedResponseAr));
  db::exec("update rewards set available=0 where id in (1,4)");
  foreach($receiversArray as $i => $receiverData) {
     db::exec("update PaypalTransactions set status='failed' where id=$id");
  }
	exit('MassPay failed: ' . print_r($httpParsedResponseAr, true));
}
/**
 * Send HTTP POST Request
 *
 * @param	string	The API method name
 * @param	string	The POST Message fields in &name=value pair format
 * @return	array	Parsed HTTP Response body
 */
function PPHttpPost($methodName_, $nvpStr_) {
	global $environment;

	// Set up your API credentials, PayPal end point, and API version.
        $API_UserName = urlencode('fassil_api1.photorewards.net');
        $API_Password = urlencode('GXNG4GR2LDT6USF6');
        $API_Signature = urlencode('A5wYGfrmnShHByAYS47ai4I1MN2fAfj9MKhVovN58vRWVyB56njvWKY8');
	$API_Endpoint = "https://api-3t.paypal.com/nvp";
	if("sandbox" === $environment || "beta-sandbox" === $environment) {
		$API_Endpoint = "https://api-3t.$environment.paypal.com/nvp";
	}
	$version = urlencode('51.0');

	// Set the curl parameters.
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);

	// Turn off the server and peer verification (TrustManager Concept).
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);

	// Set the API operation, version, and API signature in the request.
	$nvpreq = "METHOD=$methodName_&VERSION=$version&PWD=$API_Password&USER=$API_UserName&SIGNATURE=$API_Signature$nvpStr_";

	// Set the request as a POST FIELD for curl.
	curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

	// Get response from the server.
	$httpResponse = curl_exec($ch);

	if(!$httpResponse) {
		exit("$methodName_ failed: ".curl_error($ch).'('.curl_errno($ch).')');
	}

	// Extract the response details.
	$httpResponseAr = explode("&", $httpResponse);

	$httpParsedResponseAr = array();
	foreach ($httpResponseAr as $i => $value) {
		$tmpAr = explode("=", $value);
		if(sizeof($tmpAr) > 1) {
			$httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
		}
	}

	if((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
		exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
	}

	return $httpParsedResponseAr;
}
