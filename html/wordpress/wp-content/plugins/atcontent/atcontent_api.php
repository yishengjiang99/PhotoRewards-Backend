<?php

function atcontent_create_publication($ac_api_key, 
$post_title, $post_content, $paid_portion, $commercial_type, $post_published, $original_url,
$cost, $is_copyprotect, $comments
) {
    if (preg_match('/<script[^>]*src="https?:\/\/w.atcontent.com/', $paid_portion) == 1) return NULL;
    $post_splited_content = split("<!--more-->", $post_content);
    $post_face = $post_splited_content[0];
    $post_body = count($post_splited_content) > 0 ? $post_splited_content[1] : "";
    $post_paid_splited_content = split("<!--more-->", $paid_portion);
    $paid_face = $post_paid_splited_content[0];
    $paid_content = count($post_paid_splited_content) > 0 ? $post_paid_splited_content[1] : "";
    $post_content = 'Key='.
        urlencode($ac_api_key).'&AppID='.urlencode('WordPress').
        '&Title='.urlencode($post_title).
        '&CommercialType='.urlencode($commercial_type).
        '&Language='.urlencode('en').
        '&Price='.urlencode($cost).
        '&FreeFace='.urlencode($post_face).
        '&FreeContent='.urlencode($post_body).
        '&PaidFace='.urlencode($paid_face).
        '&PaidContent='.urlencode($paid_content).
        '&IsCopyProtected='.urlencode($is_copyprotect).
        '&IsPaidRepost='.urlencode($is_paidrepost).
        '&Published='.urlencode($post_published).
        '&Comments='.urlencode($comments).
        '&AddToIndex=true'.
        ( ( $original_url != NULL && strlen($original_url) > 0 ) ? ( '&OriginalUrl=' . urlencode($original_url) ) : ( '' ) ).
        '';
    return atcontent_do_post( 'http://api.atcontent.com/v1/native/create', $post_content );
}

function atcontent_api_update_publication($ac_api_key, 
$post_id, $post_title, $post_content, $paid_portion, $commercial_type, $post_published, $original_url,
$cost, $is_copyprotect, $comments
) {
    if (preg_match('/<script[^>]*src="https?:\/\/w.atcontent.com/', $post_content) == 1) return NULL;
    if (preg_match('/<script[^>]*src="https?:\/\/w.atcontent.com/', $paid_portion) == 1) return NULL;
    $post_splited_content = split("<!--more-->", $post_content);
    $post_face = $post_splited_content[0];
    $post_body = count($post_splited_content) > 0 ? $post_splited_content[1] : "";
    $post_paid_splited_content = split("<!--more-->", $paid_portion);
    $paid_face = $post_paid_splited_content[0];
    $paid_content = count($post_paid_splited_content) > 0 ? $post_paid_splited_content[1] : "";
    $post_content = 'Key='.
        urlencode($ac_api_key).'&AppID='.urlencode('WordPress').
        '&PublicationID='.urlencode($post_id).
        '&Title='.urlencode($post_title).
        '&CommercialType='.urlencode($commercial_type).
        '&Language='.urlencode('en').
        '&Price='.urlencode($cost).
        '&FreeFace='.urlencode($post_face).
        '&FreeContent='.urlencode($post_body).
        '&PaidFace='.urlencode($paid_face).
        '&PaidContent='.urlencode($paid_content).
        '&IsCopyProtected='.urlencode($is_copyprotect).
        '&Published='.urlencode($post_published).
        '&Comments='.urlencode($comments).
        '&IsPaidRepost='.urlencode($is_paidrepost).
        '&AddToIndex=true'.
        ( ( $original_url != NULL && strlen($original_url) > 0 ) ? ( '&OriginalUrl=' . urlencode($original_url) ) : ( '' ) ).
        '';
    return atcontent_do_post( 'http://api.atcontent.com/v1/native/update', $post_content );
}

function atcontent_api_update_publication_comments($ac_api_key, $post_id, $comments) {
    $post_content = 'Key='.
        urlencode($ac_api_key).'&AppID='.urlencode('WordPress').
        '&PublicationID='.urlencode($post_id).
        '&Comments='.urlencode($comments);
    return atcontent_do_post( 'http://api.atcontent.com/v1/native/update', $post_content );
}

function atcontent_api_get_nickname( $ac_api_key ) {
    $post_content = 'Key='.
        urlencode( $ac_api_key ) . '&AppID=' . urlencode( 'WordPress' );
    return atcontent_do_post( 'http://api.atcontent.com/v1/native/nickname', $post_content );
}

function atcontent_api_get_key( $nounce, $grant ) {
    $post_content = 'Nounce='. urlencode( $nounce ) . 
        '&Grant='. urlencode( $grant ) . 
        '&AppID=' . urlencode( 'WordPress' ) ;
    return atcontent_do_post( 'http://api.atcontent.com/v1/native/requestkey', $post_content );
}

function atcontent_api_pingback( $email, $status, $api_key ) {
    $post_content = 'Email='. urlencode( $email ) . 
        '&AppID=' . urlencode( 'WordPress' ) .
        ( $status != NULL ? '&Status=' . urlencode( $status ) : '' ) .
        ( $api_key != NULL ? '&APIKey=' . urlencode( $api_key ) : '' ) .
        ( defined('AC_VERSION') ? '&ExternalVersion=' . urlencode( AC_VERSION ) : '' );
    return atcontent_do_post( 'http://api.atcontent.com/v1/native/pingback', $post_content );
}

function atcontent_do_post( $url, $data ) {
    $old_error_level = error_reporting(0);
    if ( function_exists('curl_init') ) {
        $curl = curl_init();
        curl_setopt( $curl, CURLOPT_URL, $url );
        curl_setopt( $curl, CURLOPT_HEADER, 0 );
        curl_setopt( $curl, CURLOPT_POST, 1 );
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $curl, CURLOPT_POSTFIELDS, $data );
        curl_setopt( $curl, CURLOPT_USERAGENT, 'IE 10.00' );
        $res = curl_exec( $curl );
        if ( !$res ) {
	        $error = curl_error($curl).'('.curl_errno($curl).')';
	        $out = $error;
        }
        curl_close( $curl );
        $out_array = json_decode( $res, true );
    } else if ( function_exists('fsockopen') ) {
        $res = atcontent_socket_post_request( $url, $data );
        if ($res["status"] == "ok") {
            $out_array = json_decode( $res["content"], true );
        }
    } else
     {
        $context = stream_context_create(array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded' . PHP_EOL .
                            'User-Agent: IE 10.00' . PHP_EOL,
                'content' => $data,
            ),
        ));
        $res = file_get_contents(
            $file = $url,
            $use_include_path = false,
            $context);
        $out_array = FALSE;
        try {
            $out_array = json_decode($res, true);
        } catch (Exception $e) { }
    }
    error_reporting( $old_error_level );
    return $out_array;
}

function atcontent_socket_post_request( $url, $data ) {
 
    // Convert the data array into URL Parameters like a=b&foo=bar etc.
    if ( is_array ( $data ) ) $data = http_build_query($data);
 
    // parse the given URL
    $url = parse_url($url);
 
    if ($url['scheme'] != 'http') { 
        die('Error: Only HTTP request are supported !');
    }
 
    // extract host and path:
    $host = $url['host'];
    $path = $url['path'];
 
    // open a socket connection on port 80 - timeout: 30 sec
    $fp = fsockopen($host, 80, $errno, $errstr, 30);
 
    if ($fp){
 
        // send the request headers:
        fputs($fp, "POST $path HTTP/1.1\r\n");
        fputs($fp, "Host: $host\r\n");
 
        if ($referer != '')
            fputs($fp, "Referer: $referer\r\n");
 
        fputs( $fp, "Content-type: application/x-www-form-urlencoded\r\n" );
        fputs( $fp, "Content-length: " . strlen( $data ) . "\r\n" );
        fputs( $fp, "Connection: close\r\n\r\n" );
        fputs( $fp, $data );
 
        $result = ''; 
        while( ! feof( $fp ) ) {
            // receive the results of the request
            $result .= fgets( $fp, 128 );
        }
    }
    else { 
        return array(
            'status' => 'err', 
            'error' => "$errstr ($errno)"
        );
    }
 
    // close the socket connection:
    fclose( $fp );
 
    // split the result header from the content
    $result = explode( "\r\n\r\n", $result, 2 );
 
    $header = isset($result[0]) ? $result[0] : '';
    $content = isset($result[1]) ? $result[1] : '';
 
    // return as structured array:
    return array(
        'status' => 'ok',
        'header' => $header,
        'content' => $content
    );
}


?>