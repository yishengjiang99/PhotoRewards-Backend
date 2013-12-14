<?php
require_once("/var/www/lib/functions.php");
//prod keys
$key='AKIAIXNUAUZ6JDD6K4SQ';
$secret='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';
//sandbox
//$key='AKIAIN6IE7B7ZSXWRPDQ';
//$secret='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';
$hostHeader="agcws.amazon.com";
$partner="Phred";
$cversion="2008-01-01";
$defaultParams= array(
  "AWSAccessKeyId"=>$key, "Timestamp"=>gmdate("Y-m-d\TH:i:s\Z"),
  "MessageHeader.sourceId"=>$partner,
  "MessageHeader.messageType"=>"CreateGiftCardRequest",
  "MessageHeader.recipientId"=>"AMAZON",
  "MessageHeader.contentVersion"=>$cversion,
  "MessageHeader.retryCount"=>0,
  "SignatureMethod"=>"HmacSHA256",
  "SignatureVersion"=>"2",
 );

$val=$argv[1];
$count=$argv[2];
$left=db::row("select count(1) as cnt from reward_codes where reward_id=2 and given_out=0");
$left=$left['cnt'];
echo "\n$left left for $1";
if($left<200){
  buy(100,50,2);    
}

$left=db::row("select count(1) as cnt from reward_codes where reward_id=3 and given_out=0");
$left=$left['cnt'];
echo "\n$left left for $5";
if($left<20){
  buy(500,10,3);
}


function buy($value,$count,$rid){
 for($i=0;$i<$count;$i++){
  $card=buyCard($value);
  if($card->Status && $card->Status->statusCode=="SUCCESS"){

  }else{
	 break;
  }	
  $code=$card->gcClaimCode;
 
  $insert="insert ignore into reward_codes set reward_id=$rid, code=aes_encrypt('$code','supersimple');";
  echo "\n".$insert;
  db::exec($insert);
 }
}

function healthCheck(){
 global $key,$hostHeader,$secret,$defaultParams;
 $params=array_merge($defaultParams,array(
	"Action"=>"HealthCheck"));  
 $url=signUrl($params,$hostHeader,$secret);
 $ch=curl_init($url);
 echo curl_exec($ch);
}

function buyCard($amount,$rid=""){
 $amountStr=(double)$amount/100;
 if($rid=="") $rid="Phred".time().rand(1,1000);
 global $key,$hostHeader,$secret,$defaultParams;
 $params=array_merge($defaultParams,array(
  "Action"=>"CreateGiftCard",
  "gcCreationRequestId"=>$rid,
  "gcValue.amount"=>$amountStr."",
  "gcValue.currencyCode"=>"USD"));
 $url=signUrl($params,$hostHeader,$secret);
 $ch=curl_init($url); 
        
 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
 $xmlstr=curl_exec($ch);
 $xml = new SimpleXMLElement($xmlstr);
 return $xml;
}

function cancelGiftCard($requestId,$responseId){
 global $key,$hostHeader,$secret,$defaultParams;
 $params=array_merge($defaultParams,array(
        "Action"=>"CancelGiftCard",
	"gcCreationRequestId"=>$requestId,
	"gcCreationResponseId"=>$responseId
 ));
 $url=signUrl($params,$hostHeader,$secret);
 $ch=curl_init($url);
 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
 echo curl_exec($ch);
}

function voidGiftCard($requestId){
 global $key,$hostHeader,$secret,$defaultParams;
 $params=array_merge($defaultParams,array(
        "Action"=>"VoidGiftCardCreation",
        "gcCreationRequestId"=>$requestId,
 ));
 $url=signUrl($params,$hostHeader,$secret);
 $ch=curl_init($url);
 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
 echo curl_exec($ch);
}
function signUrl($parameters,$host,$secret){
    $domain = "https://$host/";
    // Write the signature
    $signature = "GET\n";
    $signature .= "$host\n";
    $signature .= "/\n";
    $sigparams=$parameters;
    ksort($sigparams);
    $first = true;
    foreach($sigparams as $key=>$param) {
    	$signature .= (!$first ? '&' : '') . rawurlencode($key) . '=' . rawurlencode($param);
    	$first = false;
    }
    $signature = hash_hmac('sha256', $signature, $secret, true);
    $signature = base64_encode($signature);
    $parameters['Signature'] = $signature;

    $url = $domain . '?';
    $first = true;
    foreach($parameters as $key=>$param) {
    	$url .= (!$first ? '&' : '') . rawurlencode($key) . '=' . rawurlencode($param);
    	$first = false;
    }
  return $url;
}
function signParams($params){
 global $hostHeader,$key;
  ksort($params);
    // Build the canonical query string
    $canonical       = '';
    foreach ($params as $key => $val) {
        $canonical  .= "$key=".rawurlencode(utf8_encode($val))."&";
    }

    // Remove the trailing ampersand
    $canonical       = preg_replace("/&$/", '', $canonical);

    // Some common replacements and ones that Amazon specifically mentions
    $canonical       = str_replace(array(' ', '+', ',', ';'), array('%20', '%20', urlencode(','), urlencode(':')), $canonical);
   
  $string_to_sign="POST\n{$hostHeader}\n/\n".$canonical;
  $signature            = base64_encode(hash_hmac('sha256', $string_to_sign, $key, true));
  return str_replace("%7E", "~", rawurlencode($signature));
}



function post($url,$fields){
 foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
 rtrim($fields_string, '&');
 $ch = curl_init();
 curl_setopt($ch,CURLOPT_URL, $url);
 curl_setopt($ch,CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_VERBOSE, 1);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
 curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
 $result = curl_exec($ch);
 curl_close($ch);
 return $result;
}

