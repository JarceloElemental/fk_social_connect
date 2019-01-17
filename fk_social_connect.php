<?php 
if (!session_id()) {
    session_start();
}

//Auth Clients
require_once dirname( __FILE__ ) . '/sdk/Google/vendor/autoload.php';
require_once dirname( __FILE__ ) . '/sdk/Facebook/autoload.php';

//Data processor
require_once dirname( __FILE__ ) . '/fk_process_data.php';

// Include required libraries
use Facebook\Facebook;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;

    class SocialConnect {
        private static $instance;
        private static $google;
        private static $facebook;
        private static $facebookHelper;
        private static $apiCredentials;

        public static function newInstance(){
            if(!self::$instance instanceof self){
                self::$instance = new self;
            }
            return self::$instance;
        }

        public function __construct(){
            $this->init_google();
            $this->init_facebook();
        }

        //initialize google with appId and appSecret 
    function init_google(){
        
        $appId = osc_get_preference('sc_g_appId', 'social_connect_google');
        $appSecret = osc_get_preference('sc_g_secret', 'social_connect_google');

        if($appId && $appSecret){
            self::$apiCredentials['google'] = true;
            self::$google = new Google_Client();
            self::$google->setClientId($appId);
            self::$google->setClientSecret($appSecret);
            self::$google->addScope("email");
            self::$google->addScope("profile");
            self::$google->setRedirectUri(osc_base_url() . 'index.php?page=custom&route=google-redirect');
            self::$google->createAuthUrl();
        } else {
            self::$apiCredentials['google'] = false;
        }
        
    }

    //initialize facebook with appId and appSecret 
    function init_facebook(){

        $appId = osc_get_preference('sc_fb_appId', 'social_connect_facebook');
        $appSecret = osc_get_preference('sc_fb_secret', 'social_connect_facebook');

        if($appId && $appSecret){            
            self::$apiCredentials['facebook'] = true;
            self::$facebook = new Facebook([
                'app_id' => $appId,
                'app_secret' => $appSecret,
                'default_graph_version' => 'v2.10',
                //'default_access_token' => '{access-token}', // optional
              ]);

              //login helper
              self::$facebookHelper = self::$facebook->getRedirectLoginHelper();
        } else {
            self::$apiCredentials['facebook'] = false;
        }
    }

    //Google methods
    function google_login_url(){
        if( self::$apiCredentials['google']) {
            return self::$google->createAuthUrl();
        } else {
            return '';
        } 

      }
      function get_google_access_token($authToken){
        self::$google->fetchAccessTokenWithAuthCode($authToken);
        $oAuth = new Google_Service_Oauth2(self::$google);
        $this->process_data($oAuth->userinfo_v2_me->get(), 'google');
      }

      function get_google_client(){
        return self::$google;
    }

    //Facebook methods
    function facebook_login_url(){
        if(self::$apiCredentials['facebook']){
            $permissions = ['email']; // Optional permissions
            return self::$facebookHelper->getLoginUrl( osc_base_url() . 'index.php?page=custom&route=facebook-redirect', $permissions);
        } else {
            return '';
        }
        
    }

    function get_facebook_access_token(){
         // OAuth 2.0 client handler helps to manage access tokens
          $oAuth2Client = self::$facebook->getOAuth2Client();
           // Exchanges a short-lived access token for a long-lived one
           $longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken(self::$facebookHelper->getAccessToken());
           // Set default access token to be used in script
           self::$facebook->setDefaultAccessToken($longLivedAccessToken);
           
           //Get profile info
           $request = self::$facebook->get( '/me?fields=name,first_name,last_name,email,link,gender,locale,cover,picture');
           
           //process returned data and login in user
          $this->process_data($request->getGraphNode()->asArray(), 'facebook');

    }


    //data processor
      function process_data($data, $provider){
        if(ProcessData::newInstance()->init($data, $provider)){
            //redirect
             header( 'Location: ' . osc_base_url() );
             exit();
        }
      }
      

    //logout
      public function resetCookies(){
        session_destroy();
     }

        
    }

?>