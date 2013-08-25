<?
error_reporting(0);
require_once("db.class.php");
function rows2table($rows){
 $t="<table border=1><tr><td>".implode("</td><td>",array_keys($rows[0]))."</td></tr>";
 foreach($rows as $r){
  $tr="<tr><td>".implode("</td><td>",array_values($r))."</td></tr>";
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
if($client_ip=='187.149.62.33') die();
if($client_ip=='173.221.152.10') die();
if($client_ip=='187.149.48.196') die();
if($client_ip=='189.174.16.108') die();
    return $client_ip;
}
