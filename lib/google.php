<?
require_once("/var/www/lib/utils.php");
require_once("/var/www/lib/db.class.php");

$google_api_key="AIzaSyDEF94baXaBfdDR2JBsdyjxrgbvGgwBb";
$google_api_key='AIzaSyBYaGwsBF_TW62QWaKcKMY49IcSwOYJHEw';
$json999_client_id="650971113077-24jo7q6o3tprj0fnqbdc7f6ev74aaprg.apps.googleusercontent.com";
$json999_callback="http://json999.com/goauth.php";
$json999_secret="TY1Q4meT-3cdYIW7YH4KZW-m";

function gaUrl(){
	$scope="https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/gan";
	return create_google_auth_url($scope);
}
function ytUrl(){
        $scope="http://gdata.youtube.com https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/yt-analytics.readonly";
        return create_google_auth_url($scope);
}

function AdsUserIdUrl(){
	$scope="https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/adexchange.buyer";
	return create_google_auth_url($scope);
}
function create_google_auth_url($scope){
	global $json999_client_id;
	global $json999_callback;
	$scope=urlencode($scope);
	$url="https://accounts.google.com/o/oauth2/auth?redirect_uri=$json999_callback&client_id={$json999_client_id}&access_type=offline&scope=$scope&response_type=code&state=$scope&approval_prompt=force";	
	return $url;
}

function create_access_token($code){
	global $json999_client_id;
	global $json999_secret;
	global $json999_callback;
	$json999_client_id=urlencode($json999_client_id);
	$post=params_to_array("client_id={$json999_client_id}&code=$code&client_secret=$json999_secret&redirect_uri=$json999_callback&grant_type=authorization_code");
	$url="https://accounts.google.com/o/oauth2/token";
	$ret=post($url,$post);
	return json_decode($ret,1);
}
function refresh_token($refresh){
	global $json999_client_id;
        global $json999_secret;
        global $json999_callback;
        $json999_client_id=urlencode($json999_client_id);
	$post=params_to_array("client_id={$json999_client_id}&refresh_token=$refresh&client_secret=$json999_secret&grant_type=refresh_token");
	$url="https://accounts.google.com/o/oauth2/token";
        $ret=post($url,$post);
	return json_decode($ret,1);
}
function guser($token){
	global $google_api_key;
	$url="https://www.googleapis.com/oauth2/v1/userinfo?access_token=$token&key=$google_api_key";
	$ret=get($url);
	return json_decode($ret,1);
}



function updateCookies($token,$user){
	setcookie("google_token_id",$token['id_token']);
        setcookie("google_access",$token['access_token']);
        $expires=$token['expires_in']+time();
        setcookie("google_access_expires",$token['expires_in']+time());
	if(isset($token['refresh_token'])){
	        setcookie("google_refresh",$token['refresh_token']);
	}
	setcookie("google_email",$user['email']);
}
function createUser($user){
	$email=$user['email'];
	if(!$uid=db::scalar("select id from user where email='$email'")){
                db::exec("insert into user set email='$email'");
                $uid=db::lastId();
        }
	return $uid;
}
function getUser($email){
	if($user=db::row("select * from user where email='$email'")){
		return $user;
	}else{
		return false;
	}	
}	
function updateTokenDb($uid,$token,$scope=''){
	if(isset($token['refresh_token'])){
		$refresh=$token['refresh_token'];
		$refreshsql=" refresh_token='$refresh', ";
	}else{
		$refreshsql='';
	}
	$tokenId=$token['id_token'];
	$access=$token['access_token'];
	$expires=$token['expires_in']+time();
        if($id=db::scalar("select id from access_tokens where token_id='$tokenId'")){
		db::exec("update access_tokens set access_token='$access', $refreshsql expires=$expires where id=$id");
	}
	elseif($id=db::scalar("select id from access_tokens where user_id=$uid and scope='$scope'")){
		db::exec("update access_tokens set access_token='$access', token_id='$tokenId', $refreshsql expires=$expires where id=$id");
	}
	else{
		db::exec("insert into access_tokens set access_token='$access', token_id='$tokenId', $refreshsql expires=$expires, user_id=$uid,scope='$scope'");
        }
}

function ad_account($access){
	$url="https://www.googleapis.com/adexchangebuyer/v1.1/accounts?access_token=$access";
	$ret=get($access);
	var_dump($ret);
        return json_decode($access,1);
}
function get_ad_deals($access){
	$url="https://www.googleapis.com/adexchangebuyer/v1.1/directdeals?access_token=$access";
	$ret=get($access);
	return json_decode($access,1);
}
