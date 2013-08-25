<?
function params_to_array($params){
	$params_arr=explode("&",$params);
	$arr=array();
	foreach($params_arr as $p){
		$t=explode('=',$p);
		$arr[$t[0]]=$t[1];
	}
	return $arr;
}

function post($url, $post){
	$ch=curl_init($url);
	curl_setopt($ch,CURLOPT_POST,true);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	$response	=	curl_exec($ch);
	curl_close($ch);
	return $response;
}	

function get($url){
	$ch=curl_init($url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        $response	=	curl_exec($ch);
        curl_close($ch);
        return $response;

}
