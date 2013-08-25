<div class="atcontent_wrap">
<?php 
         // PingBack

         if ( ! atcontent_pingback_inline() ) {
             echo "<div class=\"error\">" . 'Could not connect to atcontent.com. Contact your hosting provider.' . "</div>";
         }

         //End PingBack
$userid = wp_get_current_user()->ID;
$hidden_field_name = 'ac_submit_hidden';
$form_message = '';
$form_script = '';
$form_message_block = '';
$ac_api_key = get_user_meta($userid, "ac_api_key", true );
$ac_pen_name = get_user_meta($userid, "ac_pen_name", true );
if ( isset( $_POST[ $hidden_field_name ] ) && ( $_POST[ $hidden_field_name ] == 'Y' ) &&
    isset( $_POST[ "ac_advanced_settings" ] ) ) {
    update_user_meta( $userid, "ac_script_init", $_POST[ "ac_script_init" ] );
    $form_message .= 'Settings saved.';
}
if ( isset( $_POST[ $hidden_field_name ] ) && ( $_POST[ $hidden_field_name ] == 'Y' ) &&
    isset( $_POST[ "ac_reset_posts_processing" ] ) ) {
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
                    $ac_postid = get_post_meta($post->ID, "ac_postid", true);
                    $ac_is_process = ($ac_postid == "") ? "" : "1";
                    update_post_meta( $post->ID, "ac_is_process", $ac_is_process );
                }
            }
            $form_message .= "Post processing settings are reseted.";
    }
if ( isset( $_POST[ $hidden_field_name ] ) && ( $_POST[ $hidden_field_name ] == 'Y' ) &&
    isset( $_POST[ "ac_turn_off_pages" ] ) ) {
         $posts = $wpdb->get_results( 
	            "
	            SELECT ID, post_author
	            FROM {$wpdb->posts}
	            WHERE post_status = 'publish' 
		            AND post_author = {$userid} AND post_type = 'page'
	            "
            );

            foreach ( $posts as $post ) 
            {
                if ($post->post_author == $userid) {
                    update_post_meta( $post->ID, "ac_is_process", "2" );
                }
            }
            $form_message .= "AtContent is turned off for pages.";
    }
?>
<div class="icon32" id="icon-tools"><br></div><h2>Geek Settings</h2>
<?php 
 if (strlen($form_message) > 0) {
    $form_message_block .= <<<END
<div class="updated settings-error" id="setting-error-settings_updated"> 
<p><strong>{$form_message}</strong></p></div>
END;
}
echo $form_message_block;
  ?>

    <p>Don't use this page if you not a geek.</p>
<br><br>

<?php 
if (strlen($ac_api_key) > 0) {
    $ac_script_init = get_user_meta($userid, "ac_script_init", true );
?>
<form action="" method="POST">
<div class="wrap">
<div class="icon32" id="icon-options-general"><br></div><h3 style="padding-top: 7px;margin-bottom:0;">Advanced Settings</h3>
<br>



<div class="tool-box">
    <input type="hidden" name="<?php echo $hidden_field_name ?>" value="Y">
    <input type="hidden" name="ac_advanced_settings" value="Y">
    <p>JavaScript Code for Plugin Init Script<br>
        <textarea rows="5" cols="80" name="ac_script_init"><?php echo $ac_script_init ?></textarea><br>
        * this code will run after AtContent widget loads. If you have plugins that interact with your post content (like Lightbox, FancyBox, etc.) you should use this option.
    </p>
     <span class="submit">
        <input type="submit" name="Submit" class="button button-primary" value="<?php esc_attr_e('Save changes') ?>" />
    </span>
</div>
</div>
</form>
<br><br><br>
<h3>Service functions</h3>
<form action="" method="POST">
<div class="wrap">
    <input type="hidden" name="<?php echo $hidden_field_name ?>" value="Y">
    <input type="hidden" name="ac_reset_posts_processing" value="Y">
    <span class="submit">
        <input type="submit" name="Submit" class="button button-primary" value="<?php esc_attr_e('Reset AtContent processing settings for all posts') ?>" />
    </span>
</div>
</form>
<form action="" method="POST">
    <div class="wrap">
    <input type="hidden" name="<?php echo $hidden_field_name ?>" value="Y">
    <input type="hidden" name="ac_turn_off_pages" value="Y">
    <span class="submit">
        <input type="submit" name="Submit" class="button button-primary" value="<?php esc_attr_e('Turn off AtContent for pages') ?>" />
    </span>
</div>
</form>
<br><br>
<p>If you have any problems, ideas, feedback, questions — please <a href="http://atcontent.com/Support/">contact us</a>. We will use your help to make plugin better! :)</p>
<p> If you are interested in plugin features description, please read it on <a href="http://wordpress.org/extend/plugins/atcontent/" target="_blank">AtCotnent plugin page</a></p>

<br><br>
Diagnostic info<br>
<textarea id="diag" rows="10" cols="60">
<?php echo "Plugin version: " . AC_VERSION . "\r\n" ?>
</textarea>

<script>
    (function ($) {
        $(function () {
            var val = $("#diag").val();
            val += "jQuery: " + $().jquery + "\r\n";
            $("#diag").val(val);
        });
    })(jQuery)
</script>

<?php 
}
$form_action = admin_url( 'admin-ajax.php' );
?>
</div>
<?php
