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
if ( isset( $_POST[ $hidden_field_name ] ) && ( $_POST[ $hidden_field_name ] == 'Y' ) &&
    isset( $_POST[ "ac_api_key" ] ) ) {
    $ac_api_key = trim( $_POST[ "ac_api_key" ] );
    update_user_meta( $userid, "ac_api_key", $ac_api_key );
    $ac_pen_name = atcontent_api_get_nickname( $_POST[ "ac_api_key" ] );
    update_user_meta( $userid, "ac_pen_name", $ac_pen_name );
    $admin_url_main = admin_url("admin.php?page=atcontent/settings.php");
    ?>
<script>window.location = '<?php echo $admin_url_main ?>';</script>
<?php
    $form_message .= 'Settings saved.';
}
$ac_api_key = get_user_meta($userid, "ac_api_key", true );
$ac_pen_name = get_user_meta($userid, "ac_pen_name", true );
?>
<div class="atcontent_wrap">
<form action="" method="POST" id="disconnect-form">
    <input type="hidden" name="<?php echo $hidden_field_name ?>" value="Y">    
<?php
         if ( strlen($ac_api_key) == 0 ) {
             $form_action = admin_url( 'admin-ajax.php' );
             include("invite.php");
             ?> 
<script type="text/javascript">
    (function ($) {
        window.ac_connect_res = function (d) {
            if (d) window.location = '<?php echo admin_url( 'admin.php?page=atcontent/settings.php' ); ?>';
            else $("#ac_connect_result").html(
                    'Something get wrong. <a href="javascript:window.location.reload();">Reload page</a> and try again, please.');
        }
    })(jQuery);
</script>
<?php
         } else {
?>
<div class="wrap">
<div class="icon32" id="icon-tools"><br></div><h2>AtContent Connect Settings</h2>
<div class="tool-box">
    <script type="text/javascript">
        function disconnect() {
            jQuery("#disconnect-form").submit();
        }
    </script>
<p>You have connected blog to AtContent as <a href="https://atcontent.com/Profile/<?php echo $ac_pen_name; ?>" target="_blank"><?php echo $ac_pen_name; ?></a>.
<input type="hidden" name="ac_api_key" value="">
<button onclick="disconnect();" class="button-size-small button-color-green"><?php esc_attr_e('Disconnect') ?></button>
</p>
<?php
         }
?>
</div>
</div>
</form>
<?php
$form_action = admin_url( 'admin-ajax.php' );
?>
</div>
<div class="clear"></div>