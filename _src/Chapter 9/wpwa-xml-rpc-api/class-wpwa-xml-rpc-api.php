<?php

/*
  Plugin Name: WPWA XML-RPC API
  Plugin URI: http://www.innovativephp.com/
  Description: Creating API functions for portfolio management applications to understand the process of WordPress XML-RPC API.
  Author: Rakhitha Nimesh
  Version: 1.0
  Author URI: http://www.innovativephp.com/
 */

class WPWA_XML_RPC_API {

    public function __construct() {
        add_filter( 'xmlrpc_methods', array( $this, 'xml_rpc_api' ) );

        add_action( 'admin_menu', array( $this, 'api_settings' ) );
    }

    public function xml_rpc_api($methods) {
        $methods['wpwa.subscribeToDevelopers']  = array( $this, 'developer_subscriptions' );
        $methods['wpwa.getDevelopers']          = array( $this, 'developers_list' );
        $methods['wpwa.apiDoc']                 = array( $this, 'api_doc' );
        return $methods;
    }

    public function api_settings() {

        add_menu_page('API Settings', 'API Settings', 'follow_developer_activities', 'wpwa-api', array( $this, 'user_api_settings') );
    }

    public function user_api_settings() {

        $user_id = get_current_user_id();


        if ( isset( $_POST['api_settings'] ) ) {
            $api_token = $this->generate_random_hash();
            update_user_meta( $user_id, "api_token", $api_token );
        } else {
            $api_token = (string) get_user_meta($user_id, "api_token", TRUE);
            if ( empty($api_token) ) {
                $api_token = $this->generate_random_hash();
                update_user_meta( $user_id, "api_token", $api_token );
            }
        }


        $html = '<div class="wrap"><form action="" method="post" name="options">
                        <h2>API Credentials</h2>
                        <table class="form-table" width="100%" cellpadding="10">
                        <tbody>
                        <tr>
                        <td scope="row" align="left">
                         <label>API Token : ' . $api_token . '</label>
                        </td>
                        </tr>
                        </tbody>
                        </table>
                        <input type="submit" name="api_settings" value="Update" /></form></div>';

        echo $html;
    }

public function developer_subscriptions( $args ) {
    global $wpdb;

    $username = isset( $args['username'] ) ? $args['username'] : '';
    $password = isset( $args['password'] ) ? $args['password'] : '';

    $user = wp_authenticate( $username, $password );

    if (!$user || is_wp_error($user)) {
        return $user;
    }

    $follower_id = $user->ID;
    $api_token = (string) get_user_meta($follower_id, "api_token", TRUE);

    $token = isset( $args['token'] ) ? $args['token'] : '';
    if ( $args['token'] == $api_token) {


        $developer_id = isset( $args['developer'] ) ? $args['developer'] : 0 ;


        $user_query = new WP_User_Query( array( 'role' => 'developer', 'include' => array( $developer_id ) ) );
        if ( !empty($user_query->results) ) {
            foreach ( $user_query->results as $user ) {

                $wpdb->insert(
                        $wpdb->prefix . "subscribed_developers",
                        array(
                            'developer_id' => $developer_id,
                            'follower_id' => $follower_id
                        ),
                        array(
                            '%d',
                            '%d'
                        )
                );

                return array("success" => "Subsciption Completed.");
            }
        } else {
            return array("error" => "Invalid Developer ID.");
        }
    } else {
        return array("error" => "Invalid Token.");
    }

    return $args;
}

    public function developers_list( $args ) {
        $user_query = new WP_User_Query( array( 'role' => 'developer' ) );
        return $user_query->results;
    }

    public function api_doc() {

        $api_doc = array();

        $api_doc["wpwa.subscribeToDevelopers"] = array("authentication" => "required",
            "api_token" => "required",
            "parameters" => array("Developer ID", "API Token"),
            "result" => "Subscribing to Developer Activities"
        );

        $api_doc["wpwa.getDevelopers"] = array("authentication" => "optional",
            "api_token" => "optional",
            "parameters" => array(),
            "result" => "Retrive List of Developers"
        );

        return $api_doc;
    }

    public function generate_random_hash($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $random_string = '';
        for ($i = 0; $i < $length; $i++) {
            $random_string .= $characters[rand(0, strlen($characters) - 1)];
        }

        $random_string = wp_hash($random_string);
        return $random_string;
    }

}

new WPWA_XML_RPC_API();

?>
