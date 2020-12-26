<?php

class Script_Controller{

    public function enque_scripts(){
        add_action('wp_enqueue_scripts', array($this, 'include_scripts_styles'));

        add_action('admin_enqueue_scripts', array($this, 'include_admin_scripts_styles'));

        add_action('login_enqueue_scripts', array($this, 'include_login_scripts'));
    }

    public function include_scripts_styles(){
        global $wp_query;

        wp_register_script('wpwa_ajax', plugins_url('js/wpwa-ajax.js', dirname(__FILE__)), array("jquery"));
        wp_enqueue_script('wpwa_ajax');

        $nonce = wp_create_nonce("unique_key");

        $ajax = new WPWA_AJAX();
        $ajax->initialize();
        
        $config_array = array(
            'ajaxURL' => admin_url('admin-ajax.php'),
            'ajaxActions' => $ajax->ajax_actions,
            'ajaxNonce' => $nonce,
            'siteURL' => site_url(),
        );

        wp_localize_script('wpwa_ajax', 'wpwa_conf', $config_array);

        wp_register_style('user_styles', plugins_url('css/wpwa-style.css', dirname(__FILE__)));
        wp_enqueue_style('user_styles');

        wp_register_style('wpwa_theme', plugins_url('css/wpwa-theme.css',dirname(__FILE__)));
        wp_enqueue_style('wpwa_theme');

        wp_register_script('developerjs', plugins_url('js/wpwa-developer.js', dirname(__FILE__)), array('backbone'));
        wp_enqueue_script('developerjs');

        $developer_id = isset ($wp_query->query_vars['record_id']) ? $wp_query->query_vars['record_id'] : '0';

        $config_array = array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'developerID' => $developer_id
        );

        wp_localize_script('developerjs', 'wpwaScriptData', $config_array);


        


    }

    public function include_admin_scripts_styles(){


        wp_enqueue_script('jquery');

        if (function_exists('wp_enqueue_media')) {
            wp_enqueue_media();
        } else {
            wp_enqueue_style('thickbox');
            wp_enqueue_script('media-upload');
            wp_enqueue_script('thickbox');
        }

        wp_register_script('wpwa_file_upload', plugins_url('js/wpwa-file-uploader.js', dirname(__FILE__)), array("jquery"));
        wp_enqueue_script('wpwa_file_upload');

       // wp_enqueue_style('my-admin-theme', plugins_url('css/wp-admin.css', dirname(__FILE__)));
        
    }

    public function include_login_scripts(){
       // wp_enqueue_style('my-admin-theme', plugins_url('css/wp-admin.css', dirname(__FILE__)));
    }
}

?>