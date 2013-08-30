<?
require_once("/var/www/lib/functions.php");

$_GET=$_REQUEST;
$msgid=intval($_GET['msgid']);
db::exec("update inbox set readmsg=1 where id=$msgid");


if($msgid==3){
  $uid=intval($_GET['uid']);
  $win=rand(0,5)==2;
  $tokenstr="";
  $token=db::row("select a.token from pushtokens a join appuser b on a.idfa=b.idfa where b.id=$uid and a.app='picrewards' order by a.id desc limit 1");
  if($token){ 
	$tokenstr=$token['token'];
  }
  if(true){
     if($tokenstr!=""){
         $ctx = stream_context_create();
         stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/tools/PRProdCertKey.pem');
         stream_context_set_option($ctx, 'ssl', 'passphrase', 'prpr');
         $fp = stream_socket_client(
            'ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
         
         $msg=packageApns($tokenstr,"spinner","***SLOT MACHINE***\n\nYOU WON!!");
         $result = fwrite($fp, $msg, strlen($msg));
         
         $msg=packageApns($tokenstr,"spinner","***SLOT MACHINE***\n[E][G][C]\n[B][B][B]\n[C][B][F]\n\nYOU WON!!!");
         $result = fwrite($fp, $msg, strlen($msg));
         
         $msg=packageApns($tokenstr,"spinner","***SLOT MACHINE***\n[E][G][ ]\n[B][B][ ]\n[C][B][ ]");
         $result = fwrite($fp, $msg, strlen($msg));
         
         $msg=packageApns($tokenstr,"spinner","***SLOT MACHINE***\n[E][ ][ ]\n[B][ ][ ]\n[C][ ][ ]");
         $result = fwrite($fp, $msg, strlen($msg));
         
         $msg=packageApns($tokenstr,"spinner","***SLOT MACHINE***\n\nYou pull the lever and spin the slots...");
         $result = fwrite($fp, $msg, strlen($msg));
	 fclose($fp);  
     }
  }
}
