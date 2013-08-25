<?php

function atcontent_pingback() {
    $userid = wp_get_current_user()->ID;
    $email = wp_get_current_user()->user_email;
    $ac_api_key = get_user_meta($userid, "ac_api_key", true );
    if ( current_user_can( 'edit_posts' ) ) {

        $status = 'Installed';

        if (strlen($ac_api_key) > 0) { 
            $status = 'Connected'; 
        } else {
            $status = 'Disconnected';
        }

        atcontent_api_pingback( $email, $status, $ac_api_key );

	    // generate the response
	    $response = json_encode( array( 'IsOK' => true ) );
 
	    // response output
	    header( "Content-Type: application/json" );
	    echo $response;
    }
 
    // IMPORTANT: don't forget to "exit"
    exit;
}

function atcontent_pingback_inline(){
    $userid = wp_get_current_user()->ID;
    $email = wp_get_current_user()->user_email;
    $ac_api_key = get_user_meta($userid, "ac_api_key", true );
    if ( current_user_can( 'edit_posts' ) || current_user_can( 'publish_posts' ) ) {
        $status = 'Installed';
        if ( strlen( $ac_api_key ) > 0 ) { 
            $status = 'Connected'; 
        } else {
            $status = 'Disconnected';
        }
        $res = atcontent_api_pingback( $email, $status, $ac_api_key );
        if ( is_array( $res ) && $res["IsOK"] == TRUE ) return TRUE; 
    }
    return FALSE;
}

function atcontent_activate() {
    global $wpdb;
    $wp_user_search = $wpdb->get_results("SELECT ID, user_email FROM $wpdb->users ORDER BY ID");
    foreach ( $wp_user_search as $user ) {
        $userid = $user->ID;
        $email = $user->user_email;
        $ac_api_key = get_user_meta($userid, "ac_api_key", true );
        if ( user_can( $userid, 'edit_posts' ) ) {
            $status = 'Activated';

            if (strlen($ac_api_key) > 0) {
                $status = 'Connected'; 
            } else {
                $ac_pen_name = get_user_meta( intval($userid), "ac_pen_name", true );
                if ( strlen( $ac_pen_name ) > 0) { 
                    $status = 'Disconnected';
                }
            }

            atcontent_api_pingback( $email, $status, $ac_api_key );
        }
    }
}

function atcontent_deactivate() {
    global $wpdb;
    $wp_user_search = $wpdb->get_results("SELECT ID, user_email FROM $wpdb->users ORDER BY ID");
    foreach ( $wp_user_search as $user ) {
        $userid = $user->ID;
        $email = $user->user_email;
        $ac_api_key = get_user_meta($userid, "ac_api_key", true );
        if ( user_can( $userid, 'edit_posts' ) ) {
            $status = 'Deactivated';
            atcontent_api_pingback( $email, $status, $ac_api_key );
        }
    }
}

function atcontent_uninstall() {
    global $wpdb;
    $wp_user_search = $wpdb->get_results("SELECT ID, user_email FROM $wpdb->users ORDER BY ID");
    foreach ( $wp_user_search as $user ) {
        $userid = $user->ID;
        $email = $user->user_email;
        $ac_api_key = get_user_meta($userid, "ac_api_key", true );
        if ( user_can( $userid, 'edit_posts' ) ) {
            $status = 'Uninstalled';
            atcontent_api_pingback( $email, $status, $ac_api_key );
        }
    }
}