<?php 
// PingBack

         if ( ! atcontent_pingback_inline() ) {
             echo "<div class=\"error\">" . 'Could not connect to atcontent.com. Contact your hosting provider.' . "</div>";
         }

         //End PingBack
?>
<div class="atcontent_wrap">
<?php
    $userid = wp_get_current_user()->ID;
    $ac_api_key = get_user_meta($userid, "ac_api_key", true );
    $posts = $wpdb->get_results( 
	            "
	            SELECT ID, post_title, post_author
	            FROM {$wpdb->posts}
	            WHERE post_status = 'publish' 
		            AND post_author = {$userid} AND post_type = 'post'
	            "
            );
    $posts_count = 0;
    $imported_count = 0;
    foreach ( $posts as $post ) 
    {
         $ac_postid = get_post_meta($post->ID, "ac_postid", true);
         if ( strlen( $ac_postid ) > 0 ) $imported_count++;
         $posts_count++;
    }

?>
<style>
    
    button.button-color-orange:hover, .likebutton.b_orange:hover, .qq-upload-button-hover .likebutton.b_orange {
    background: none repeat scroll 0 0 #F5A200;
}
    button.button-color-orange:active, .likebutton.b_orange:active {
    background: none repeat scroll 0 0 #E76D00;
}
</style>
<div class="wrap">
<div class="icon32" id="icon-tools"><br></div><h2>AtContent CopyLocator</h2>
<div class="tool-box">
    <p class="b-big-text">Find all illegal copies of your content across the Internet</p>
    <?php if ( $imported_count == 0 ) { 
        $link = "http://atcontent.com/CopyLocator/"; ?>
        <?php if ( $posts_count == 0 ) {  ?>
            <p>You don't have publications yet. Write something first!</p>
        <?php } else { ?>
            <p>You have <?php echo $posts_count ?> publications, but you should sync it with AtContent first. 
                Follow <a href="<?php echo admin_url("admin.php?page=atcontent/settings.php"); ?>">AtContent Dashboard page</a> and click "Sync with AtContent"</p>
        <?php } ?>
    <?php } else { 
        $link = "http://atcontent.com/CopyLocator/Create/"; ?>
        <?php if ( $imported_count < $posts_count ) { ?>
            <p>You have <?php echo $imported_count ?> publications. 
                And <?php echo $posts_count - $imported_count ?> more available for <a href="<?php echo admin_url("admin.php?page=atcontent/settings.php"); ?>">sync</a>.</p>
        <?php } else { ?>
            <p>You have <?php echo $imported_count ?> publications.</p>
        <?php } ?>
    <?php }?>
    <?php if ( strlen( $ac_api_key ) == 0 ) {
     $link = "javascript:connectFirst();";
      ?>
        <script type="text/javascript">
            function connectFirst() {
                alert("Please, connect with AtContent first");
                document.location = '<?php echo admin_url("admin.php?page=atcontent/settings.php"); ?>';
            }
        </script>
    <?php } ?>
            <p><a href="<?php echo $link; ?>" class="likebutton b_big b_orange">Find illegal copies</a></p>
</div>
</div>
</div>