<?php
if( Params::getParam('plugin_action') == 'done' ) {
        //facebook preference
        osc_set_preference('sc_fb_appId',  Params::getParam('sc_fb_appId'),  'social_connect_facebook', 'STRING') ;
        osc_set_preference('sc_fb_secret', Params::getParam('sc_fb_secret'), 'social_connect_facebook', 'STRING') ;

        //google preference
        osc_set_preference('sc_g_appId',  Params::getParam('sc_g_appId'),  'social_connect_google', 'STRING') ;
        osc_set_preference('sc_g_secret', Params::getParam('sc_g_secret'), 'social_connect_google', 'STRING') ;

        if(osc_version()<300) {
            echo '<div style="text-align:center; font-size:22px; background-color:#00bb00;"><p>' . __('Congratulations. The plugin is now configured','social_connect') . '.</p></div>' ;
            osc_reset_preferences();
        } else {
            ob_get_clean();
            osc_add_flash_ok_message(__('Congratulations. The plugin is now configured', 'social_connect'), 'admin');
            osc_admin_render_plugin(osc_plugin_folder(__FILE__) . 'config.php');
        }


    }

?>
<style>
.auth-creds {
    padding: 10px 0 10px 10px;
    border: 1px solid #a2a2a2;
    margin: 0 10px 10px 0px;
    overflow: hidden;
}
.auth-creds input {
    width: 100%;
}
</style>
<div id="settings_form" style="border: 1px solid #ccc; background: #eee; ">
    <div style="padding: 20px;">
        <div style="float: left; width: 50%;">
            <fieldset>
                <h2><?php _e('Social Connect Options', 'social_connect') ; ?></h2>
                    <form name="sc_form" id="sc_form" action="<?php echo osc_admin_base_url(true) ; ?>" method="GET" enctype="multipart/form-data" >
                    <input type="hidden" name="page" value="plugins" />
                    <input type="hidden" name="action" value="renderplugin" />
                    <input type="hidden" name="file" value="fk_social_connect/admin/config.php" />
                    <input type="hidden" name="plugin_action" value="done" />
                    <div class="auth-creds">
                    <?php _e("Please enter your <strong>Facebook</strong> appId and secret*:", 'facebook') ; ?><br />
                    <p style="display: table; height: 26px;"><label style="width: 60px; display: table-cell; vertical-align: middle; text-align: center;">appId:</label> <input type="text" name="sc_fb_appId" id="sc_fb_appId" value="<?php echo osc_get_preference('sc_fb_appId','social_connect_facebook') ; ?>" maxlength="100" size="60" /></p>
                    <p style="display: table; height: 26px;"><label style="width: 60px; display: table-cell; vertical-align: middle; text-align: center;">secret:</label> <input type="text" name="sc_fb_secret" id="sc_fb_secret" value="<?php echo osc_get_preference('sc_fb_secret', 'social_connect_facebook') ; ?>" maxlength="100" size="60" /></p>
                    </div>
                    <div class="auth-creds">
                    <?php _e("Please enter your <strong>Google</strong> appId and secret*:", 'google') ; ?><br />
                    <p style="display: table; height: 26px;"><label style="width: 60px; display: table-cell; vertical-align: middle; text-align: center;">appId:</label> <input type="text" name="sc_g_appId" id="sc_g_appId" value="<?php echo osc_get_preference('sc_g_appId','social_connect_google') ; ?>" maxlength="100" size="60" /></p>
                    <p style="display: table; height: 26px;"><label style="width: 60px; display: table-cell; vertical-align: middle; text-align: center;">secret:</label> <input type="text" name="sc_g_secret" id="sc_g_secret" value="<?php echo osc_get_preference('sc_g_secret', 'social_connect_google') ; ?>" maxlength="100" size="60" /></p>
                    </div>
                    <button class="btn" type="submit"><?php _e('Update', 'social_connect') ; ?></button>
                    </form>
            </fieldset>
        </div>
        <div style="float: left; width: 50%;">
            <fieldset>
            <h2><?php _e("Social Connect Help", 'social_connect') ; ?></h2>

            <h3><?php _e("What is Social Connect Plugin?", 'social_connect') ; ?></h3>
            <?php _e("Social Connect plugin allows your users to log into your webpage using their Facebook or Google accounts", 'social_connect') ; ?>.
            <br/>
            <br/>
            <h3><?php _e("Auth Credentials", 'social_connect') ; ?></h3>
            <?php _e('To get an appId and secret key (needed to use Facebook or Google login on your website) follow the instructions on these links','facebook') ; ?>: 
            <p>Facebook</p>
            <a rel="nofollow" target="_blank" href="https://developers.facebook.com/docs/apps/">https://developers.facebook.com/docs/apps/</a>
            <p>Google</p>
            <a rel="nofollow" target="_blank" href="https://console.developers.google.com/apis/credentials/">https://console.developers.google.com/apis/credentials/</a>

            <p>Make sure you add the following, under Authorized redirect URIs, in your google OAuth client, in google developers console </p>
            <pre>
            http://{Your Domian}/index.php?page=custom&route=google-redirect
            </pre>
            <br />
            
            <h3><?php _e("Usage", 'social_connect') ; ?></h3>
            <?php _e("To use Facebook or Google login in your website you should include the following code where you want it to appear",'social_connect') ; ?>:<br/>
            <pre>
            // To include buttons

            &lt;?php sc_facebook_login_button(); ?&gt;

            &lt;?php sc_google_login_button(); ?&gt;
            </pre>
            <br />
            <pre>
            // To include just the links

            &lt;?php sc_facebook_login_url(); ?&gt;

            &lt;?php sc_google_login_url(); ?&gt;
            </pre>
            <br />
            <div style="font-size: small;">By 
            <a rel="nofollow" target="_blank" href="http://www.github.com/farhankk360">Farhan Ullah</a>
            </div>
            <br/>
            </fieldset>
        </div>
        <div style="clear: both;"></div>
    </div>
</div>