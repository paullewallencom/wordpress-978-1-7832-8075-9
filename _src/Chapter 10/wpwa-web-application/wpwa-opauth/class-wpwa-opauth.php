<?php


class WPWA_Opauth {

    public function __construct() {
        //add_action('template_redirect', array($this, 'load_opauth'));

        //add_action('login_addons', array($this, 'login_addons'));

    }

    public function initialize() {
        add_action('wpwa_login_addons', array($this, 'login_addons'));
    }



    public function login_addons() {
        echo '<li><a href="facebook">Facebook</a></li>
              <li><a href="twitter">Twitter</a></li>
              <li><a href="linkedin">LinkedIn</a></li>
           ';
    }

    public function load_opauth() {
        global $wp;

        $url_prefix = 'user/login/';
        $allowed_requests = array(  $url_prefix.'twitter',
                $url_prefix.'twitter/oauth_callback',
                $url_prefix.'linkedin',
                $url_prefix.'linkedin/oauth2callback',
                $url_prefix.'facebook',
                $url_prefix.'facebook/int_callback' );

        if (in_array($wp->request, $allowed_requests) || $wp->request == "opauth_success") {



            define('CONF_FILE', dirname(__FILE__) . '/' . 'opauth.conf.php');
            define('OPAUTH_LIB_DIR', dirname(__FILE__) . '/lib/Opauth/');

            /**
             * Load config
             */
            if (!file_exists(CONF_FILE)) {
                trigger_error('Config file missing at ' . CONF_FILE, E_USER_ERROR);
                exit();
            }
            require CONF_FILE;

            /**
             * Instantiate Opauth with the loaded config
             */
            require OPAUTH_LIB_DIR . 'Opauth.php';
            $Opauth = new Opauth($config);

            if ($wp->request == 'opauth_success') {
                $response = null;

                switch ($Opauth->env['callback_transport']) {
                    case 'session':
                        session_start();
                        $response = $_SESSION['opauth'];
                        unset($_SESSION['opauth']);
                        break;
                    case 'post':
                        $response = unserialize(base64_decode($_POST['opauth']));
                        break;
                    case 'get':
                        $response = unserialize(base64_decode($_GET['opauth']));
                        break;
                    default:
                        echo '<strong style="color: red;">Error: </strong>Unsupported callback_transport.' . "<br>\n";
                        break;
                }

                $provider = isset($response['auth']['provider']) ? $response['auth']['provider'] : '';
                $username = '';
                $first_name = '';
                $email = '';
                $pass = wp_generate_password();
                $user_info = $response['auth']['info'];

                switch ($provider) {
                    case 'Facebook':
                        $username = $user_info['email'];
                        $first_name = $user_info['name'];
                        $email = $user_info['email'];

                        break;
                    case 'Twitter':
                        $username = $user_info['nickname'];
                        $first_name = $user_info['name'];
                        $email = '';

                        break;
                    case 'LinkedIn':
                        $username = $user_info['email'];
                        $first_name = $user_info['name'];
                        $email = $user_info['email'];

                        break;

                }



                if (username_exists($username)) {
                    $user = get_userdatabylogin($username);
                    wp_set_auth_cookie($user->ID, false, is_ssl());
                    wp_redirect(admin_url(''));
                } else {

                    $user_details = array('user_login' => $username, 'user_pass' => $pass,
                            'first_name' => $first_name, "user_email" => $email, "role" => "developer");

                    $user_id = wp_insert_user($user_details);

                    if (is_wp_error($user_id)) {
                        echo $user_id->get_error_message();
                        exit;
                    } else {

                        wp_set_auth_cookie($user_id, false, is_ssl());
                        wp_redirect(admin_url('profile.php'));
                    }
                }
            }
        }
    }



}

//$opauth = new WPWA_Opauth();
