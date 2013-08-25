<?php
         $userid = wp_get_current_user()->ID;
         $hidden_field_name = 'ac_submit_hidden';
         $form_message = '';
         $form_script = '';
         $form_message_block = '';
         if ( isset( $_POST[ $hidden_field_name ] ) && ( $_POST[ $hidden_field_name ] == 'Y' ) &&
              isset( $_POST[ "ac_api_key" ] ) ) {
             $ac_api_key = trim( $_POST[ "ac_api_key" ] );
             update_user_meta( $userid, "ac_api_key", $ac_api_key );
             $ac_pen_name = atcontent_api_get_nickname( $_POST[ "ac_api_key" ] );
             update_user_meta( $userid, "ac_pen_name", $ac_pen_name );
             $form_message .= 'Settings saved.';
         }
         $ac_api_key = get_user_meta($userid, "ac_api_key", true );
         $ac_pen_name = get_user_meta($userid, "ac_pen_name", true );
         $img_url = plugins_url( 'assets/logo.png', __FILE__ );
?>
<div class="update-nag"><img style="vertical-align:bottom;" src="<?php echo $img_url; ?>" alt=""> <a href="https://atcontent.com/Statistics/Distribution/">Check new visual detailed distribution statistics</a> of your publications!</div>
<?php

         // PingBack

         if ( ! atcontent_pingback_inline() ) {
             echo "<div class=\"error\">" . 'Could not connect to atcontent.com. Contact your hosting provider.' . "</div>";
         }

         //End PingBack

         if ( ( strlen($ac_api_key) > 0 ) && isset($_POST[ $hidden_field_name ]) && ( $_POST[ $hidden_field_name ] == 'Y' ) &&
              isset( $_POST[ "ac_import" ] ) && ( $_POST[ "ac_import" ] == 'Y' ) ) {
            
            $copyProtection = isset( $_POST["ac_copyprotect"] ) && $_POST["ac_copyprotect"] == "Y" ? 1 : 0;
            update_user_meta($userid, "ac_copyprotect", $copyProtection);
            $paidRepost = isset($_POST["ac_paidrepost"]) && $_POST["ac_paidrepost"] == "Y" ? 1 : 0;
            update_user_meta($userid, "ac_paidrepost", $paidRepost);
            $paidRepostCost = isset($_POST["ac_paidrepostcost"]) && is_numeric($_POST["ac_paidrepostcost"]) ? doubleval($_POST["ac_paidrepostcost"]) : 2.5;
            update_user_meta($userid, "ac_paidrepostcost", $paidRepostCost);
            $importComments = isset($_POST["ac_comments"]) && $_POST["ac_comments"] == "Y" ? 1 : 0;
            update_user_meta($userid, "ac_is_import_comments", $importComments);

            $ac_with_import = isset( $_POST['ac_with_import'] ) && $_POST['ac_with_import'] == "Y";

            $ac_excerpt_image_remove = (isset( $_POST[ "ac_excerpt_image_remove" ] ) && $_POST[ "ac_excerpt_image_remove" ] == "Y") ? "1" : "0";
            update_user_meta( $userid, "ac_excerpt_image_remove", $ac_excerpt_image_remove );
            $ac_excerpt_no_process = (isset( $_POST[ "ac_excerpt_no_process" ] ) && $_POST[ "ac_excerpt_no_process" ] == "Y") ? "1" : "0";
            update_user_meta( $userid, "ac_excerpt_no_process", $ac_excerpt_no_process );
            $ac_comments_disable = (isset( $_POST[ "ac_comments_disable" ] ) && $_POST[ "ac_comments_disable" ] == "Y") ? "1" : "0";
            update_user_meta( $userid, "ac_comments_disable", $ac_comments_disable );
            $ac_hint_panel_disable = (isset( $_POST[ "ac_hint_panel_disable" ] ) && $_POST[ "ac_hint_panel_disable" ] == "Y") ? "1" : "0";
            update_user_meta( $userid, "ac_hint_panel_disable", $ac_hint_panel_disable );
            $form_message .= 'Settings saved.';

            if ($ac_with_import) {

                $ac_reset = isset( $_POST['ac_reset'] ) && $_POST['ac_reset'] == "Y";
                if ( $ac_reset ) $form_message .= "Reset done. ";

                $posts_id = array();
                $posts_title = array();

                $posts = $wpdb->get_results( 
	                "
	                SELECT ID, post_title, post_author
	                FROM {$wpdb->posts}
	                WHERE post_status = 'publish' 
		                AND post_author = {$userid} AND post_type = 'post'
	                "
                );

                foreach ( $posts as $post ) 
                {
                    if ($post->post_author == $userid) {
                        array_push( $posts_id, $post->ID );
                        array_push( $posts_title, addcslashes( $post->post_title, "'\\" ) );
                        if ($ac_reset) {
                            update_post_meta( $post->ID, "ac_is_process", "2" );
                            update_post_meta( $post->ID, "ac_cost", "" );
                            update_post_meta( $post->ID, "ac_paidrepostcost", "" );
                            update_post_meta( $post->ID, "ac_is_paidrepost", "" );
                            update_post_meta( $post->ID, "ac_is_copyprotect", "" );
                            update_post_meta( $post->ID, "ac_is_import_comments", "" );
                            update_post_meta( $post->ID, "ac_type", "" );
                        }
                    }	
                }

            
                $postIDs = join( "','" , $posts_id );
                $postTitles = join( "','" , $posts_title );
                $form_action = admin_url( 'admin-ajax.php' );
                $form_message .= '<div id="importStatus">Sync started.</div><div id="importResult">Processed 0 of ...</div>Note: Updating posts takes few seconds, please be patient.<div id="importDetails"></div>';
                $form_script = <<<END
<script type="text/javascript">
    var postIDs = ['{$postIDs}'];
    var postTitles = ['{$postTitles}'];
    var retryIDs = [];
    var postInfo = [];
    for (var i in postIDs) {
        postInfo[i] = {id: postIDs[i], title: postTitles[i], status: "queued"};
    }
    var imported = 0;
    var errors = 0;
    function getStatus() {
        var r = {created:0, updated:0, skiped:0, error:0, queued:0};
        for (var i in postInfo) {
            if (isNaN(i)) continue;
            if (postInfo[i].status == "created") r.created++;
            else if (postInfo[i].status == "updated") r.updated++; 
            else if (postInfo[i].status == "skiped") r.skiped++; 
            else if (postInfo[i].status == "error") r.error++; 
            else r.queued++; 
        }
        return r;
    }
    function doRetry() {
        for (var i in postInfo) {
            if (postInfo[i].status == "error") doImport(postInfo[i].id);
        }
    }
    function doImport(i) {
        jQuery.ajax({url: '{$form_action}', 
                         type: 'post', 
                         data: {action: 'atcontent_import', 
                                postID: postIDs[i], 
                                copyProtection: {$copyProtection}, 
                                paidRepost: {$paidRepost}, 
                                cost: {$paidRepostCost}, 
                                comments: {$importComments}},
                         dataType: "json",
                         success: function(d){
                                postInfo[i] = postInfo[i] || {};
                                if (d.IsOK) {
                                    postInfo[i].title = postTitles[i];
                                    postInfo[i].status = d.AC_action;
                                    imported++;
                                    jQuery("#importResult").html("Processed " + imported + " of " + postIDs.length);
                                } else {
                                    errors++;
                                    retryIDs[errors] = i;
                                    postInfo[i].status = "error";
                                    postInfo[i].error =  "WordPress hosting error for \"" + postTitles[i] + "\" (" + d + ")";
                                }
                                if (getStatus().queued == 0) doResult();
                            },
                         error: function(d, s, e) {
                                 postInfo[i] = postInfo[i] || {};
                                 if (e == 'timeout') { doImport(i); return; }
                                 var err = "WordPress hosting error";
                                 if (e.length > 0) err += ": " + e;
                                 errors++;
                                 retryIDs[errors] = i;
                                 postInfo[i].status = "error";
                                 postInfo[i].error = err + " for \"" + postTitles[i] + "\"";
                                 if (getStatus().queued == 0) doResult();
                             },
                         });
    }
    function doResult(){
        var j = jQuery,
            s = getStatus(),
            h = "";
            h += (s.created > 0 ? s.created + " posts created<br>" : "");
            h += (s.updated > 0 ? s.updated + " posts updated<br>" : "");
            h += (s.skiped > 0 ? s.skiped + " posts skiped<br>" : "");
            h += (s.error > 0 ? s.error + " posts processed with errors<br>" : "");
            h += "You got " + (s.created + s.updated) + " backlinks<br>";
            h += "<a href=\"javascript:getDetails();\">Get details</a>";
        j("#importResult").html(h);
        j("#importStatus").html("<b>Sync completed.</b>");
    }
    function getDetails(){
         var j = jQuery, h = "";         
         for (var i in postInfo) {
             if (isNaN(i)) continue;
             h += "\"" + postInfo[i].title + "\" ";
             if (postInfo[i].status == "created") h += "created";
             if (postInfo[i].status == "updated") h += "updated";
             if (postInfo[i].status == "skiped") h += "skiped";
             if (postInfo[i].status == "error") h += "processed with error: " + postInfo[i].error;
             h += "<br>";
         }
         if (getStatus().error > 0) {
             h += "<a href=\"javascript:doRetry();\">Do retry for posts with errors</a>";
         }
         j("#importDetails").html(h);
    }
    jQuery(function(){
        for (var i in postIDs) {
            doImport(i);
        }
    });
</script>
END;
            }
         }
         if (strlen($form_message) > 0) {
             $form_message_block .= <<<END
<div class="updated settings-error" id="setting-error-settings_updated"> 
<p><strong>{$form_message}</strong></p></div>
END;
            echo $form_message_block; 
         }
         
?>

<div class="atcontent_wrap">

<?php if ( strlen( $ac_api_key ) == 0 ) { ?>
    <?php include("invite.php"); ?>
    <hr />
    <br>
<?php } ?>
<div class="wrap">
    <div class="icon32" id="icon-tools"><br></div><h2>AtContent&nbsp;Dashboard</h2>
</div>

<div class="wrap" style="width: 640px; float: left;">
<p>To brand existing posts, get backlinks and additional readership from AtContent click "Sync with AtContent".<br>You also can choose additional options.</p>
    <?php 
             $ac_copyprotect = get_user_meta($userid, "ac_copyprotect", true );
             if (strlen($ac_copyprotect) == 0) $ac_copyprotect = "1";
             $ac_paidrepost = get_user_meta($userid, "ac_paidrepost", true );
             if (strlen($ac_paidrepost) == 0) $ac_paidrepost = "0";
             $ac_paidrepostcost = get_user_meta($userid, "ac_paidrepostcost", true );
             if (strlen($ac_paidrepostcost) == 0) $ac_paidrepostcost = "2.50";
             $ac_is_import_comments = get_user_meta($userid, "ac_is_import_comments", true );
             if (strlen($ac_is_import_comments) == 0) $ac_is_import_comments = "1";

             $ac_copyprotect_checked = $ac_copyprotect == "1" ? "checked=\"checked\"" : "";
             $ac_paidrepost_checked = $ac_paidrepost == "1" ? "checked=\"checked\"" : "";
             $ac_is_import_comments_checked = $ac_is_import_comments == "1" ? "checked=\"checked\"" : "";

             echo $form_script;
?>
<script>
    function showCool() {
        jQuery("#whyCool").toggle();
    }
    function saveForm(withImport) {
        var j = jQuery;
        if (withImport == 1) {
            j("#ac_with_import").val("Y");
        } else {
            j("#ac_with_import").val("N");
        }
        <?php if ( strlen( $ac_api_key ) == 0 ) { ?>
            alert('Please, connect with AtContent first');
        <?php } else { ?>
            j("#import-form").submit();
        <?php } ?>
    }
</script>
<form action="" method="POST" name="import-form" id="import-form">
<div class="tool-box">
    
    <input type="hidden" name="<?php echo $hidden_field_name ?>" value="Y">
    <input type="hidden" name="ac_import" value="Y">
    <input type="hidden" name="ac_with_import" id="ac_with_import" value="Y">
    <p><input type="checkbox" name="ac_copyprotect" id="ac_copyprotect" value="Y" <?php echo $ac_copyprotect_checked ?>> Prevent plagiarism for synced posts</p>
    <p><input type="checkbox" name="ac_paidrepost" id="ac_paidrepost" value="Y" <?php echo $ac_paidrepost_checked ?>> Turn on paid repost for synced posts.
    Price is, $
    <input type="text" name="ac_paidrepostcost" id="ac_paidrepostcost" value="<?php echo $ac_paidrepostcost ?>"></p>
    <p><input type="checkbox" name="ac_comments" id="ac_comments" value="Y" <?php echo $ac_is_import_comments_checked ?>> Sync post comments with AtContent plugin comments <a href="javascript:showCool();">(why it's cool)</a><br> 
    <span id="whyCool" style="display: none;">* People will be able to see each other comments from different sites and<br> 
        even answer to each other from different sites!<br>
        This way you engage your users and get more comments!</span></p>

    <p><input type="checkbox" name="ac_reset" value="Y">
        Reset all AtContent settings. Settings above will be applied to all publications.</p>

        <a href="javascript:saveForm(1);" class="likebutton b_orange"><?php esc_attr_e('Sync with AtContent') ?></a>
   
</div><br><br><br>
</div>
<div style="float:right;">
    <br>
<?php
    $banner_url = strlen ( $ac_api_key ) == 0 ? "javascript:alert('Please, connect with AtContent first');" : admin_url("admin.php?page=atcontent/copylocator.php"); 
    $ref_url = strlen ( $ac_api_key ) == 0 || !is_string( $ac_pen_name ) ? "http://atcontent.com/RefUrl/CPlase/" . base64_encode("http://wordpress.org/extend/plugins/atcontent/") : 
        "http://atcontent.com/RefUrl/" . $ac_pen_name . "/" . base64_encode("http://wordpress.org/extend/plugins/atcontent/"); 
?>
    
    <div class="atcontent_banner"">
        <h2>Invite your friends to AtContent</h2>
        <p>For every friend who installs AtContent plugin on their blog,<br> we'll give you <b>free</b> check for plagiarism for up to 100 of your posts!</p>
        <h3>Invite friends</h3>

        <textarea id="inviteText" style="width: 100%;height: 45px;">Jump up in search, reach new readership, brand and control your content with #AtContent. Free WP plugin for your blog</textarea>

        <p style="font-size: 1.2em;">&nbsp;&nbsp; <b>↓</b> Send by email or share anywhere!</p>
        <div style="float:left" id="addthis_share">
<!-- AddThis Button BEGIN -->
<div class="addthis_toolbox addthis_default_style addthis_32x32_style">
    <a class="addthis_button_email"></a>
    <a class="addthis_button_facebook"></a>
    <a class="addthis_button_twitter"></a>
    <a class="addthis_button_linkedin"></a>
    <a class="addthis_button_pinterest_share"></a>
    <a class="addthis_button_google_plusone_share"></a>
    <a class="addthis_button_stumbleupon"></a> 
    <a class="addthis_button_digg"></a>
    <a class="addthis_button_compact"></a>
    <a class="addthis_counter addthis_bubble_style"></a>

</div>
<script type="text/javascript">
    var addthis_share =
    {
        url: '<?php echo $ref_url; ?>',
        title: 'WordPress with AtContent — even better. Check it!',
        description: 'Jump up in search, reach new readership, brand and control your content with #AtContent. Free WP plugin for your blog',
        email_template: 'plugin_invite',
    };
    var ac_j = jQuery;
    ac_j(function(){
        window.addthis_share.title = ac_j("#inviteText").val();
        ac_j("#inviteText").bind('input propertychange', function() {
            if(this.value.length){
                window.addthis_share.title = this.value;
                window.addthis_share.description = this.value;            
                addthis.toolbox(".addthis_toolbox");
                
            }
        });
    });
</script>
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-514ee41e167a87dc"></script>
<!-- AddThis Button END -->
        
    </div>
        <div style="clear:both;">&nbsp;</div>
    </div>
    

</div>
<div style="clear:both;">&nbsp;</div>

<?php
    $ac_excerpt_image_remove = get_user_meta($userid, "ac_excerpt_image_remove", true );
    if (strlen($ac_excerpt_image_remove) == 0) $ac_excerpt_image_remove = "0";
    $ac_excerpt_no_process = get_user_meta($userid, "ac_excerpt_no_process", true );
    if (strlen($ac_excerpt_no_process) == 0) $ac_excerpt_no_process = AC_NO_PROCESS_EXCERPT_DEFAULT;
    $ac_comments_disable = get_user_meta($userid, "ac_comments_disable", true );
    if (strlen($ac_comments_disable) == 0) $ac_comments_disable = "0";
    $ac_hint_panel_disable = get_user_meta($userid, "ac_hint_panel_disable", true );
    if (strlen($ac_hint_panel_disable) == 0) $ac_hint_panel_disable = "0";
    $ac_script_init = get_user_meta($userid, "ac_script_init", true );

    $ac_excerpt_image_remove_checked = "";
    if ($ac_excerpt_image_remove == "1") $ac_excerpt_image_remove_checked = "checked=\"checked\"";
    $ac_excerpt_no_process_checked = "";
    if ($ac_excerpt_no_process == "1") $ac_excerpt_no_process_checked = "checked=\"checked\"";
    $ac_comments_disable_checked = "";
    if ($ac_comments_disable == "1") $ac_comments_disable_checked = "checked=\"checked\"";
    $ac_hint_panel_disable_checked = "";
    if ($ac_hint_panel_disable == "1") $ac_hint_panel_disable_checked = "checked=\"checked\"";
    

?>
<br>

<div class="wrap">
<div class="icon32" id="icon-options-general"><br></div><h3 style="padding-top: 14px;margin-bottom:0;">Advanced Settings</h3>
<br>

<div class="tool-box">
    <p><input type="checkbox" name="ac_excerpt_no_process" value="Y" <?php echo $ac_excerpt_no_process_checked ?>>
    Turn off plugin features for a main page (should be marked for sites with not standard themes)</p>
    <p><input type="checkbox" name="ac_comments_disable" value="Y" <?php echo $ac_comments_disable_checked ?>>
    Turn off plugin comments</p>
    <p><input type="checkbox" name="ac_hint_panel_disable" value="Y" <?php echo $ac_hint_panel_disable_checked ?>>
    Turn off line "Share  and repost and get $$$..."</p>
     
    <a href="javascript:saveForm(0);" class="likebutton b_green"><?php esc_attr_e('Save Settings') ?></a>
    
</div>
</div>
</form>
<br><br><br>
<p><a href="http://wordpress.org/extend/plugins/atcontent/" target="_blank">AtCotnent plugin page</a> &nbsp; 
    <a href="http://atcontent.com/Support/" target="_blank">Support</a> &nbsp; 
    <a href="http://atcontent.com/About/" target="_blank">About AtContent</a> &nbsp; 
    <a href="http://atcontent.com/Privacy/" target="_blank">Privacy Policy</a> &nbsp; 
    <a href="http://atcontent.com/Terms/" target="_blank">Terms and Conditions</a> &nbsp; 
</p>

</div>