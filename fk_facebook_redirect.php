<?php 
require_once dirname( __FILE__ ) . '/fk_social_connect.php';

   // Try to get access token
    try {    
         if(isset($_GET['state']) && isset($_GET['code'])){
            return SocialConnect::newInstance()->get_facebook_access_token();
        }
    } catch(FacebookResponseException $e) {
        echo 'Graph returned an error: ' . $e->getMessage();
        exit;
    } catch(FacebookSDKException $e) {
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
        header( 'Location: ' . osc_base_url() );
    }

?>