<?php $form_action = admin_url( 'admin-ajax.php' ); ?>
<div class="atcontent_invite">
    <h1>With AtContent plugin your site jumps up in search results!</h1>
    <h1>You get new readership &amp; control your content</h1>
    <hr />
        <table>
            <tr>
                <td style="width: 275px;">
                    <div class="discl">
                        We connect your site<br>
                        with AtContent.<br>
                        This way we provide<br>
                        new readership and<br>
                        backlinks for all your posts.<br>
                        We help you<br>
                        to control content<br>
                        across the Internet,<br>
                        prevent plagiarism<br>
                        and much more.</div>
                    <div class="addit">
                        Every minute<br>
                        in the world is activated<br>
                        one more AtContent plugin.
                    </div>
                </td>
                <td style="text-align: center;">
                    <iframe width="425" height="313" src="http://www.youtube.com/embed/1U4zq5qhRmk?rel=0&showinfo=0" frameborder="0" allowfullscreen></iframe>
                    <br><br>
                    <div id="ac_connect_result"></div>
<iframe id="ac_connect" src="http://atcontent.com/Auth/WordPressConnect/?ping_back=<?php echo $form_action ?>" style="width:302px;height:50px;" frameborder="0" scrolling="no"></iframe>
<script type="text/javascript">
    (function ($) {
        window.ac_connect_res = function (d) {
            if (d) window.location.reload();
            else $("#ac_connect_result").html( 
                    'Something get wrong. <a href="javascript:window.location.reload();">Reload page</a> and try again, please.');
        }
    })(jQuery);
</script>
                    <br><br>
                </td>
            </tr>
        </table>
    </div>