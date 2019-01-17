<?php 

require_once dirname( __FILE__ ) . '/fk_social_connect.php';

// Try to get access token

    if(isset($_GET['code'])) {
        SocialConnect::newInstance()->get_google_access_token($_GET['code']);
    }
    

?>