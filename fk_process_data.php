<?php

class ProcessData extends DAO {
    private static $instance;

    public static function newInstance(){
        if(!self::$instance instanceof self){
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function __construct(){
        parent::__construct();
        $this->setTableName('social_connect');
        $this->setPrimaryKey('fk_i_user_id');
        $this->setFields(array('fk_i_user_id', 'social_uid', 'via'));
    }

    public function init($user_profile, $provider) {

            $this->dao->select( $this->getFields() );
            $this->dao->from( $this->getTableName() );
            $this->dao->where( 'social_uid', $user_profile['id'] );
            $rs = $this->dao->get();

            if( ( $rs !== false ) && ( $rs->numRows() === 1 ) ) {
                $gUser = $rs->row();
                if( count($gUser) > 0 ) {
                    require_once osc_lib_path() . 'osclass/UserActions.php';
                    $uActions = new UserActions( false );
                    $logged   = $uActions->bootstrap_login( $gUser['fk_i_user_id'] );

                    switch( $logged ) {
                        case 0: osc_add_flash_error_message( __( 'The username doesn\'t exist', 'social_connect' ) );
                        break;
                        case 1: osc_add_flash_error_message( __( 'The user has not been validated yet', 'social_connect' ) );
                        break;
                        case 2: osc_add_flash_error_message( __( 'The user has been suspended', 'social_connect' ) );
                        break;
                    }
                     return true;
                }
            }

            if( !isset($user_profile['email']) ) {
                osc_add_flash_error_message( __('Some error occured trying to connect with google.', 'social_connect') );
                header( 'Location: ' . osc_base_url() );
                exit();
            }

            $manager = User::newInstance();
            $oscUser = $manager->findByEmail( $user_profile['email'] );
            // exists on our DB, we merge both accounts
            if( count($oscUser) > 0 ) {
                require_once osc_lib_path() . 'osclass/UserActions.php';
                $uActions = new UserActions( false );

                $manager->dao->from( $this->getTableName() );
                $manager->dao->set( 'fk_i_user_id', $oscUser['pk_i_id'] );
                $manager->dao->set( 'social_uid', $user_profile['id'] );
                $manager->dao->set( 'via', $provider );
                $manager->dao->insert();
                osc_add_flash_ok_message( __( "You already have an user with this e-mail address. We've merged your accounts", 'social_connect' ) );

                // activate user in case is not activated
                $manager->update( array('b_active' => '1')
                                 ,array('pk_i_id' => $oscUser['pk_i_id']) );
                $logged = $uActions->bootstrap_login( $oscUser['pk_i_id'] );
               
            } else {
                // Auto-register him
                return $this->register_user( $user_profile, $provider );
            }
            
           return true;     
}


    public function import( $file ){
            $path = osc_plugin_resource( $file );
            $sql  = file_get_contents( $path );

            if( !$this->dao->importSQL( $sql ) ) {
                throw new Exception( __('Error importing the database structure', 'social_connect') );
            }
        }

        //uninstall plugin
    public function uninstall(){
            $this->resetCookies();
            $this->dao->query( 'DROP TABLE ' . $this->getTableName() );
        }


        private function register_user($user, $provider){
            $manager = User::newInstance();

            $input['s_name']      = $user['name'];
            $input['s_email']     = $user['email'];
            $input['s_password']  = sha1( osc_genRandomPassword() );
            $input['dt_reg_date'] = date( 'Y-m-d H:i:s' );
            $input['s_secret']    = osc_genRandomPassword();

            $email_taken = $manager->findByEmail( $input['s_email'] );
            if($email_taken == null) {
                $manager->insert( $input );
                $userID = $manager->dao->insertedId();

                $manager->dao->from( $this->getTableName() );
                $manager->dao->set( 'fk_i_user_id', $userID );
                $manager->dao->set( 'social_uid', $user['id'] );
                $manager->dao->set( 'via', $provider );
                $result = $manager->dao->replace();

                if( $result == false ) {
                    // error inserting user
                    return false;
                }

                osc_run_hook( 'user_register_completed', $userID );

                $userDB = $manager->findByPrimaryKey( $userID );

                if( osc_notify_new_user() ) {
                    osc_run_hook( 'hook_email_admin_new_user', $userDB );
                }

                if(osc_version()>=310) {
                    $manager->update( array('b_active' => '1', 's_username' => $userID)
                                    ,array('pk_i_id' => $userID) );
                } else {
                        $manager->update( array('b_active' => '1')
                            ,array('pk_i_id' => $userID) );
                }

                osc_run_hook('hook_email_user_registration', $userDB);
                osc_run_hook('validate_user', $userDB);

                //login new user
                require_once osc_lib_path() . 'osclass/UserActions.php';
                $uActions = new UserActions( false );
                $uActions->bootstrap_login( $userID );

                osc_add_flash_ok_message( sprintf( __('Your account has been created successfully', 'social_connect' ), osc_page_title() ) );
                
                return true;
            }
        }
}
?>