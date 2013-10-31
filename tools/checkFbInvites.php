<?php
$turl='https://graph.facebook.com/oauth/access_token?client_id=146678772188121&client_secret=de49dfd8e172bfb840036a53e44c5d7c&grant_type=client_credentials';
$token=file_get_contents($turl);
$tstr=str_replace("access_token=","",$token);

require_once("/var/www/lib/functions.php");
$invites=db::rows("select * from fbinvites where created>date_sub(now(), interval 2 day)");
$ch=curl_init();
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
foreach($invites as $i){
 $rqid=$i['request_id']."_".$i['to_fbid'];
 $check="https://graph.facebook.com/$rqid?access_token=$tstr";
 curl_setopt($ch,CURLOPT_URL,$check);
 $ret=curl_exec($ch);
echo "\n".$ret;
}
