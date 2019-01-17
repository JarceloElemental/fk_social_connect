<?php
/*
Plugin Name: Social Connect
Plugin URI: https://www.github.com/farhankk360/osclass-social-connect-plugin
Description: Login using your Facbook or Google accounts
Version: 1.0.0
Author: Farhan Ullah
Author URI: https://www.github.com/farhankk360
Short Name: social_connect
Plugin update URI: https://www.github.com/farhankk360/osclass-social-connect-plugin
*/

//class
require_once dirname( __FILE__ ) . '/fk_social_connect.php';
//Data processor
require_once dirname( __FILE__ ) . '/fk_process_data.php';

//auth redirect routes
osc_add_route('google-redirect',
              'google-redirect/([0-9]+)/(.+)', 
              'google-redirect/', osc_plugin_folder(__FILE__).'fk_google_redirect.php');

osc_add_route('facebook-redirect',
              'facebook-redirect/([0-9]+)/(.+)', 
              'facebook-redirect/', osc_plugin_folder(__FILE__).'fk_facebook_redirect.php');              

//facebook login url
function sc_facebook_login_url(){
    return SocialConnect::newInstance()->facebook_login_url();
}

//google login url
function sc_google_login_url(){
    return SocialConnect::newInstance()->google_login_url();
}

//facebook login button
function sc_facebook_login_button(){
    if(!osc_is_web_user_logged_in()){
        echo '<div><a href="' . sc_facebook_login_url() . '">' . __( 'Login with Facebook', 'social_connect' ) . '</a></div>';
    }
}
//google login button
function sc_google_login_button(){
    if(!osc_is_web_user_logged_in()){
        echo '<div><a href="' . sc_google_login_url() . '">' . __( 'Login with Google', 'social_connect' ) . '</a></div>';
    }
}


//plugin install
function sc_call_after_install() {
    ProcessData::newInstance()->import( 'fk_social_connect/struct.sql' );
    //set preferences
    osc_set_preference('sc_fb_appId' , '', 'social_connect_facebook', 'STRING');
    osc_set_preference('sc_fb_secret', '', 'social_connect_facebook', 'STRING');

    osc_set_preference('sc_g_appId' , '', 'social_connect_google', 'STRING');
    osc_set_preference('sc_g_secret', '', 'social_connect_google', 'STRING');
}

function sc_call_after_uninstall() {
    ProcessData::newInstance()->uninstall();
    //delete preferences
    osc_delete_preference( 'sc_fb_appId' , 'social_connect_facebook' );
    osc_delete_preference( 'sc_fb_secret', 'social_connect_facebook' );
    
    osc_delete_preference( 'sc_g_appId' , 'social_connect_google' );
    osc_delete_preference( 'sc_g_secret', 'social_connect_google' );
    
}

function sc_delete_user( $userID ) {
    $osc = ProcessData::newInstance();
    $osc->deleteByPrimaryKey( $userID );
    sc_logout();
}

// LOGOUT in 3.1+ versions
function sc_logout() {
    SocialConnect::newInstance()->resetCookies();
}

// LOGOUT in 2.x and 3.0.x versions
function sc_check_logout() {
    if(Params::getParam("page")=="main" && Params::getParam("action")=="logout" && osc_version()<310) {
        sc_logout();
    }
}

// Display help
function sc_config() {
    osc_admin_render_plugin( osc_plugin_path( dirname(__FILE__) ) . '/admin/config.php' );
}

// extend manage users
function sc_extend_manage_users($row, $aRow) {
    $user_id = $aRow['pk_i_id'];
    $user_exist = false;
    $manager = User::newInstance();
    $manager->dao->select();
    $manager->dao->from( DB_TABLE_PREFIX.'social_connect' );
    $manager->dao->where( 'fk_i_user_id', $user_id );
    $result = $manager->dao->get();

    var_dump($result->result());
    if($result != false) {
        if($result->result()!=array()) {
            $row['email'] = $row['email'] . ' - '. 'via '.$result->result()[0]['via'] ;
        }
    }
    return $row;
}
osc_add_filter('users_processing_row', 'sc_extend_manage_users');

// This is needed in order to be able to activate the plugin
osc_register_plugin( osc_plugin_path( __FILE__ ), 'sc_call_after_install' );
// This is a hack to show a Configure link at plugins table (you could also use some other hook to show a custom option panel)
osc_add_hook( osc_plugin_path( __FILE__ ) . '_configure', 'sc_config' );
// This is a hack to show a Uninstall link at plugins table (you could also use some other hook to show a custom option panel)
osc_add_hook( osc_plugin_path( __FILE__ ) . '_uninstall', 'sc_call_after_uninstall' );

osc_add_hook('delete_user', 'sc_delete_user');
osc_add_hook('init',        'sc_check_logout');
osc_add_hook('logout',      'sc_logout');
?>