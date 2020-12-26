<?php

class WPWA_File_Uploader {

    /*
    * Include neccessary actions and filters to initialize the plugin.
    *
    * @param  -
    * @return -
    */
    public function __construct() {
        //add_action('admin_enqueue_scripts', array($this, 'include_scripts'));
        //add_filter('upload_mimes', array($this, 'filter_mime_types'));
    }

    public function initialize() {
        add_filter('upload_mimes', array($this, 'filter_mime_types'));
    }

    /*
    * Modify the allowed mime types for specific post type.
    *
    * @param  array List of mime types generated from WordPress core and
    *               through plugins
    * @return array List of updated mime types
    */
    function filter_mime_types($mimes) {
        $mimes = array(
                'jpg|jpeg|jpe' => 'image/jpeg',
        );

        do_action_ref_array('wpwa_custom_mimes', array(&$mimes));

        return $mimes;
    }

    

}

//$file_uploader = new WPWA_File_Uploader();


/*
 * Extending the plugin with the same file.
 * Ideally you should be using a seperate plugin to extend the
 * features of core plugins.
*/
function wpwa_custom_mimes(&$mimes) {
    $mimes['png'] = 'image/png';
}

add_action("wpwa_custom_mimes", "wpwa_custom_mimes");


?>
