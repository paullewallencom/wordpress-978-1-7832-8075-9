<?php

/*
  Plugin Name: WPWA AJAX
  Plugin URI:
  Description: Common library for making ajax requests
  Version: 1.0
  Author: Rakhitha Nimesh
  Author URI: http://www.innovativephp.com/
  License: GPLv2 or later
 */

class WPWA_AJAX {

    private $ajax_actions;

    /*
     * Configuring and intializing ajax files and actions
     *
     * @param  -
     * @return -
    */
    public function __construct() {

        $this->configure_actions();
        add_action( 'wp_enqueue_scripts', array( $this, 'include_scripts' ) );
    }

    /*
     * Confire the application specific AJAX actions array and
     * load the AJAX actions bases on supplied parameters
     *
     * @param  -
     * @return -
    */
    public function configure_actions() {
        $this->ajax_actions = array(
            "sample_key" => array("action" => "sample_action", "function" => "sample_function_name"),
            "sample_key1" => array("action" => "sample_action1", "function" => "sample_function_name1"),
        );

        /*
         * Add the AJAX actions into WordPress
         */
        foreach ( $this->ajax_actions as $custom_key => $custom_action ) {

            if ( isset( $custom_action["logged"] ) && $custom_action["logged"] ) {
                // Actions for users who are logged in
                add_action( "wp_ajax_" . $custom_action['action'], array( $this, $custom_action["function"] ) );
            } else if ( isset( $custom_action["logged"] ) && !$custom_action["logged"]) {
                // Actions for users who are not logged in
                add_action( "wp_ajax_nopriv_" . $custom_action['action'], array( $this, $custom_action["function"] ) );
            } else {
                // Actions for users who are logged in and not logged in
                add_action( "wp_ajax_nopriv_" . $custom_action['action'], array( $this, $custom_action["function"] ) );
                add_action( "wp_ajax_" . $custom_action['action'], array( $this, $custom_action["function"] ) );
            }
        }
    }

    /*
     * Sample functions for handling AJAX request
     *
     * @param  -
     * @return -
    */
    function sample_function_name() {

        $nonce = $_POST['nonce'];

        if ( !wp_verify_nonce($nonce, 'unique_key') )
            die('Unauthorized request!');

        echo json_encode( $_POST );
        exit;
    }

    /*
     * Include AJAX plugin specific scripts and pass the neccessary data.
     *
     * @param  -
     * @return -
    */
    public function include_scripts() {
        global $post;

        wp_enqueue_script('jquery');

        wp_register_script('wpwa_ajax', plugins_url('js/wpwa-ajax.js', __FILE__), array("jquery"));
        wp_enqueue_script('wpwa_ajax');

        $nonce = wp_create_nonce("unique_key");

        $config_array = array(
            'ajaxURL' => admin_url('admin-ajax.php'),
            'ajaxActions' => $this->ajax_actions,
            'ajaxNonce' => $nonce,
        );

        wp_localize_script('wpwa_ajax', 'wpwa_conf', $config_array);
    }

}

$file_uploader = new WPWA_AJAX();
?>
