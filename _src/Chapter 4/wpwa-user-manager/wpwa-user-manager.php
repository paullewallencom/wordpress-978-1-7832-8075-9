<?php
/*
  Plugin Name: WPWA User Manager
  Plugin URI:
  Description: User management module for the portfolio management application.
  Author: Rakhitha Nimesh
  Version: 1.0
  Author URI: http://www.innovativephp.com/
*/

class WPWA_User_Manager {

    public function __construct() {
        // Creates all the user types
        register_activation_hook( __FILE__ , array( $this, 'add_application_user_roles' ) );
        // Remove unused user roles
        register_activation_hook( __FILE__, array( $this, 'remove_application_user_roles' ) );
        // Create custom capabilities for user roles
        register_activation_hook( __FILE__, array( $this, 'add_application_user_capabilities' ) );

        register_activation_hook( __FILE__, array( $this, 'flush_application_rewrite_rules' ) );

        add_action( 'template_redirect', array( $this, 'front_controller' ) );

        add_action( 'init', array( $this, 'manage_user_routes' ) );

        //add_action('wpwa_register_user', array($this, 'validate_user'));
        add_action( 'wpwa_register_user', array( $this, 'register_user' ) );
        add_action( 'wpwa_login_user', array( $this, 'login_user' ) );
        add_action( 'wpwa_activate_user', array( $this, 'activate_user' ) );
        add_filter( 'authenticate', array( $this, 'authenticate_user' ), 30, 3 );

        add_filter( 'query_vars', array( $this, 'manage_user_routes_query_vars' ) );

        add_action( 'wp_enqueue_scripts', array( $this, 'generate_styles' ) );
    }

//    /*
//     * Add extra validation on user registration
//     *
//     * @param  -
//     * @return void
//     */
//    public function validate_user() {
//        remove_action('wpwa_register_user', array($this, 'register_user'));
//    }

    /*
     * Add new user roles to application on activation
     *
     * @param  -
     * @return void
    */

    public function add_application_user_roles() {
        add_role( 'follower', 'Follower', array( 'read' => true ) );
        add_role( 'developer', 'Developer', array( 'read' => true ) );
        add_role( 'member', 'Member', array( 'read' => true ) );
    }

    /*
     * Remove existing user roles from application on activation
     *
     * @param  -
     * @return void
    */

    public function remove_application_user_roles() {
        remove_role( 'author' );
        remove_role( 'editor' );
        remove_role( 'contributor' );
        remove_role( 'subscriber' );
    }

    /*
     * Add capabilities to user roles on activation
     *
     * @param  -
     * @return void
    */

    public function add_application_user_capabilities() {
        $role = get_role( 'follower' );
        $role->add_cap( 'follow_developer_activities' );

        $developer = get_role("developer");
        $custom_developer_capabilities = array(
                "edit_posts",
                "edit_private_posts",
                "edit_published_posts",
                "publish_posts",
                "read",
                "delete_posts",
                "manage_project_type",
                "edit_project_type",
                "delete_project_type",
                "assign_project_type",
        );

        foreach ($custom_developer_capabilities as $capability) {
            $developer->add_cap($capability);
        }

        $role = get_role('administrator');
        $custom_admin_capabilities = array("manage_project_type",
                "edit_project_type",
                "delete_project_type",
                "assign_project_type",
        );

        foreach ($custom_admin_capabilities as $capability) {
            $role->add_cap($capability);
        }
    }

    /*
     * Activate user account using the link
     *
     * @param  -
     * @return void
    */

    public function activate_user() {

        $activation_code = isset( $_GET['activation_code'] ) ? $_GET['activation_code'] : '';
        $message = '';

        // Get activation record for the user
        $user_query = new WP_User_Query(
                array(
                        'meta_key' => 'activation_code',
                        'meta_value' => $activation_code
                )
        );

        $users = $user_query->get_results();

        // Check and update activation status
        if ( !empty($users) ) {
            $user_id = $users[0]->ID;
            update_user_meta( $user_id, 'activation_status', 'active' );
            $message = 'Account activated successfully. ';
        } else {
            $message = 'Invalid Activation Code';
        }

        include dirname(__FILE__) . '/templates/info.php';
        exit;
    }

    /*
     * Log the user into the system
     *
     * @param  -
     * @return void
    */
    public function login_user() {
        if ( $_POST ) {

            $errors = array();

            $username = isset ( $_POST['username'] ) ? $_POST['username'] : '';
            $password = isset ( $_POST['password'] ) ? $_POST['password'] : '';
            
            if ( empty( $username ) )
                array_push( $errors, 'Please enter a username.' );

            if ( empty( $password ) )
                array_push( $errors, 'Please enter password.' );

            if(count($errors) > 0){
                include dirname(__FILE__) . '/templates/login.php';
                exit;
            }

            $credentials = array();
            
            $credentials['user_login']      = $username;
            $credentials['user_login']      = sanitize_user( $credentials['user_login'] );
            $credentials['user_password']   = $password;
            $credentials['remember']        = false;

            $user = wp_signon( $credentials, false );
            if ( is_wp_error( $user ) )
                array_push( $errors, $user->get_error_message() );
            else
                wp_redirect( home_url() );
        }

        if ( !is_user_logged_in() ) {
            include dirname(__FILE__) . '/templates/login.php';
        } else {
            wp_redirect( home_url() );
        }
        exit;
    }

    /*
     * Execute extra validations in user authentication
     *
     * @param  object  User
     * @param  string  Username of the authenticated user
     * @param  string  Password of the authenticated user
     * @return object  User Object or Error Object
    */
    public function authenticate_user( $user, $username, $password ) {
        if ( !in_array( 'administrator', (array) $user->roles ) ) {
            $active_status = '';
            $active_status = get_user_meta( $user->data->ID, 'activation_status', true );

            if ( 'inactive' == $active_status ) {
                $user = new WP_Error( 'denied', __('<strong>ERROR</strong>: Please activate your account.' ) );
            }
        }
        return $user;
    }

    /*
     * Register new application user from frontend
     *
     * @param  -
     * @return void
    */

    public function register_user() {
        if ( $_POST ) {

            $errors = array();

            $user_login = ( isset ( $_POST['user'] ) ? $_POST['user'] : '' );
            $user_email = ( isset ( $_POST['email'] ) ? $_POST['email'] : '' );
            $user_type  = ( isset ( $_POST['user_type'] ) ? $_POST['user_type'] : '' );

            // Validating user data
            if ( empty( $user_login ) )
                array_push( $errors, 'Please enter a username.' );

            if ( empty( $user_email ) )
                array_push( $errors, 'Please enter e-mail.' );

            if ( empty( $user_type ) )
                array_push( $errors, 'Please enter user type.' );


            $sanitized_user_login = sanitize_user( $user_login );

            if ( !empty($user_email) && !is_email( $user_email ) )
                array_push( $errors, 'Please enter valid email.');
            elseif ( email_exists( $user_email ) )
                array_push( $errors, 'User with this email already registered.' );

            if ( empty( $sanitized_user_login ) || !validate_username( $user_login ) )
                array_push( $errors, 'Invalid username.' );
            elseif ( username_exists( $sanitized_user_login ) )
                array_push( $errors, 'Username alreay exists.' );

            if ( empty( $errors ) ) {
                $user_pass  = wp_generate_password();
                $user_id    = wp_insert_user( array('user_login' => $sanitized_user_login,
                                                        'user_email' => $user_email,
                                                        'role' => $user_type,
                                                        'user_pass' => $user_pass)
                                            );


                if ( !$user_id ) {
                    array_push( $errors, 'Registration failed.' );
                } else {
                    $activation_code = $this->random_string();

                    update_user_meta( $user_id, 'activation_code', $activation_code );
                    update_user_meta( $user_id, 'activation_status', 'inactive' );
                    wp_new_user_notification( $user_id, $user_pass, $activation_code );

                    $success_message = "Registration completed successfully. Please check your email for activation link.";
                }

                if ( !is_user_logged_in() ) {
                    include dirname(__FILE__) . '/templates/login.php';
                    exit;
                }
            }
        }
        if ( !is_user_logged_in() ) {
            include dirname(__FILE__) . '/templates/register.php';
            exit;
        }
    }

    /*
     * Front controller for handling custom routing
     *
     * @param  -
     * @return void
    */

    public function front_controller() {
        global $wp_query;
        $control_action = isset ( $wp_query->query_vars['control_action'] ) ? $wp_query->query_vars['control_action'] : ''; ;
        switch ( $control_action ) {
            case 'register':
                do_action( 'wpwa_register_user' );
                break;

            case 'login':
                do_action( 'wpwa_login_user' );
                break;

            case 'activate':
                do_action( 'wpwa_activate_user' );
                break;
        }
    }

    /*
     * Add custom routinng rules
     *
     * @param  -
     * @return void
    */

    public function manage_user_routes() {
        add_rewrite_rule( '^user/([^/]+)/?', 'index.php?control_action=$matches[1]', 'top' );
    }

    /*
     * Flush and rest application rewrite rules on activation
     *
     * @param  -
     * @return void
    */

    public function flush_application_rewrite_rules() {
        $this->manage_user_routes();
        flush_rewrite_rules();
    }

    /*
     * Add custom query variables to WordPress
     *
     * @param  array  List of built-in query variables of WordPress
     * @return void
    */

    public function manage_user_routes_query_vars( $query_vars ) {
        $query_vars[] = 'control_action';
        return $query_vars;
    }

    /*
     * Generate random string for activation code
     *
     * @param  -
     * @return string
    */
    public function random_string() {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randstr = '';
        for ( $i = 0; $i < 15; $i++ ) {
            $randstr .= $characters[rand(0, strlen( $characters ))];
        }
        return $randstr;
    }

    /*
     * Include neccessary styles for the plugin
     *
     * @param  -  
     * @return void
    */
    public function generate_styles() {
        wp_register_style( 'user_styles', plugins_url( 'css/style.css', __FILE__ ) );
        wp_enqueue_style( 'user_styles' );
    }

}

$user_manege = new WPWA_User_Manager();





/*
 *  Overriden version of wp_new_user_notification function
 *  for sending activation code
*/

if ( !function_exists( 'wp_new_user_notification' ) ) {

    function wp_new_user_notification($user_id, $plaintext_pass = '', $activate_code = '') {

        $user = new WP_User($user_id);

        $user_login = stripslashes($user->user_login);
        $user_email = stripslashes($user->user_email);

        $message = sprintf(__('New user registration on %s:'), get_option('blogname')) . '\r\n\r\n';
        $message .= sprintf(__('Username: %s'), $user_login) . '\r\n\r\n';
        $message .= sprintf(__('E-mail: %s'), $user_email) . '\r\n';

        @wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), get_option('blogname')), $message);

        if (empty($plaintext_pass))
            return;

        $activate_link = site_url() . "/user/activate/?activation_code=$activate_code";

        $message = __('Hi there,') . '\r\n\r\n';
        $message .= sprintf(__('Welcome to %s! Please activate your account using the link:'), get_option('blogname')) . '\r\n\r\n';
        $message .= sprintf(__('<a href="%s">%s</a>'), $activate_link, $activate_link) . '\r\n';
        $message .= sprintf(__('Username: %s'), $user_login) . '\r\n';
        $message .= sprintf(__('Password: %s'), $plaintext_pass) . '\r\n\r\n';

        wp_mail($user_email, sprintf(__('[%s] Your username and password'), get_option('blogname')), $message);
    }

}
