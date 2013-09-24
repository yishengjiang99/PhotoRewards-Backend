<?
error_reporting(0);
require_once("db.class.php");
function rows2table($rows){
 $t="<table border=1><tr><td>".implode("</td><td>",array_keys($rows[0]))."</td></tr>";
 foreach($rows as $r){
  $tr="<tr>";
  foreach($r as $k=>$v){
	if(stripos($k,"link")===0){
		$k=str_replace("link","",$k);
		$v="<a target=_blank href=$v>$k</a>";
	}
        else $v=urldecode($v);
	$tr.="<td>$v</td>";	
  }
  $tr.="</tr>";
  $t.=$tr;
 }
 $t.="</table>";
 return $t;
}

function getIP() {
    $ipString=@getenv("HTTP_X_FORWARDED_FOR");
    $addr = explode(",",$ipString);
    return $addr[sizeof($addr)-1];
} 
function getRealIP(){
    if( $_SERVER['HTTP_X_FORWARDED_FOR'] != '' ) { 
        $client_ip = ( !empty($_SERVER['REMOTE_ADDR']) ) ? $_SERVER['REMOTE_ADDR'] :(( !empty($_ENV['REMOTE_ADDR']) ) ? $_ENV['REMOTE_ADDR'] : "unknown" );

        // los proxys van añadiendo al final de esta cabecera
        // las direcciones ip que van "ocultando". Para localizar la ip real
        // del usuario se comienza a mirar por el principio hasta encontrar
        // una dirección ip que no sea del rango privado. En caso de no
        // encontrarse ninguna se toma como valor el REMOTE_ADDR

        $entries = split('[, ]', $_SERVER['HTTP_X_FORWARDED_FOR']);
        reset($entries);
        while (list(, $entry) = each($entries)){
            $entry = trim($entry);
            if ( preg_match("/^([0-9]+.[0-9]+.[0-9]+.[0-9]+)/", $entry, $ip_list) ){
                // http://www.faqs.org/rfcs/rfc1918.html
                $private_ip = array(
                    '/^0./',
                    '/^127.0.0.1/',
                    '/^192.168..*/',
                    '/^172.((1[6-9])|(2[0-9])|(3[0-1]))..*/',
                    '/^10..*/');
                $found_ip = preg_replace($private_ip, $client_ip, $ip_list[1]);
                if ($client_ip != $found_ip){
                    $client_ip = $found_ip;
                    break;
                }
            }
        }
    } else {
        $client_ip = ( !empty($_SERVER['REMOTE_ADDR']) ) ? $_SERVER['REMOTE_ADDR'] : ( ( !empty($_ENV['REMOTE_ADDR']) ) ? $_ENV['REMOTE_ADDR'] : "unknown" );
        if ($client_ip == 'unknown') {
            if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
            {
                $ip=$_SERVER['HTTP_CLIENT_IP'];}
                elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
                {
                    $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
                }
                else { 
                    $ip=$_SERVER['REMOTE_ADDR'];
                }
                $client_ip = $ip;
            }
        }
if($client_ip=='171.224.195.35') die();
if($client_ip=='173.221.152.10') die();
if($client_ip=='42.112.243.5') die();
if($client_ip=='187.149.48.196') die();
if($client_ip=='189.174.16.108') die();
if($client_ip=='82.67.220.15') die();
if($client_ip=='199.217.118.4') die();
if($client_ip=='174.36.47.170') die();
if($client_ip=='217.150.241.243') die();
if($client_ip=='187.149.55.192') die();
if($client_ip=='173.44.51.43') die();

if($client_ip=='171.236.234.216') die();
    return $client_ip;
}

function packageApns($deviceToken,$badge,$iam='',$url=''){
// Create the payload body
 $body['aps'] = array(
        'alert' => $badge,
        'sound' => 'default',
        'custom_key1'=>'hi',
        );

 if($iam!=""){
   $body['aps']['msg']=$iam;
 }
 if($url!=''){
   $body['aps']['url']=$url; 
 }
 $payload = json_encode($body);
 $msg=chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

 return	$msg;
}

function email($to,$subject,$body,$from="support@json999.com"){
        $host="smtp.gmail.com";
        $headers = "From: $from\r\n" .
        'X-Mailer: PHP/' . phpversion() . "\r\n" .
        "MIME-Version: 1.0\r\n" .
        "Content-Type: text/html; charset=utf-8\r\n" .
        "Content-Transfer-Encoding: 8bit\r\n\r\n";
        ini_set("SMTP",$host);
        ini_set("smtp_port","25");
        ini_set("sendmail_from","$from");
        return mail($to, $subject, $body, $headers);
}
   function check_email_address($email) {
        // First, we check that there's one @ symbol, and that the lengths are right
        if (!preg_match("/^[^@]{1,64}@[^@]{1,255}$/", $email)) {
            // Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
            return false;
        }
        // Split it into sections to make life easier
        $email_array = explode("@", $email);
        $local_array = explode(".", $email_array[0]);
        for ($i = 0; $i < sizeof($local_array); $i++) {
            if (!preg_match("/^(([A-Za-z0-9!#$%&'*+\/=?^_`{|}~-][A-Za-z0-9!#$%&'*+\/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$/", $local_array[$i])) {
                return false;
            }
        }
        if (!preg_match("/^\[?[0-9\.]+\]?$/", $email_array[1])) { // Check if domain is IP. If not, it should be valid domain name
            $domain_array = explode(".", $email_array[1]);
            if (sizeof($domain_array) < 2) {
                return false; // Not enough parts to domain
            }
            for ($i = 0; $i < sizeof($domain_array); $i++) {
                if (!preg_match("/^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$/", $domain_array[$i])) {
                    return false;
                }
            }
        }

        return true;
    }
function bitlyLink($longurl){
 if($row=db::row("select * from biturl where longurl='$longurl'")){
   return $row['biturl'];
 }
 $bit="https://api-ssl.bitly.com/v3/shorten?access_token=7764e72e15451500910f26e0350207ba38f4fba3&longUrl=".urlencode($longurl);
 $rr=json_decode(file_get_contents($bit),1);
 $biturl=$rr['data']['url'];
 db::exec("insert into biturl set longurl='$longurl',biturl='$biturl'");
 return $biturl;
}
