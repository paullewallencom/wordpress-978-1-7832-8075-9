<?php

/*
  Plugin Name: WPWA Custom Posts Manager
  Plugin URI:
  Description: Core data management using Custom Post Types for the portfolio management application.
  Author: Rakhitha Nimesh
  Version: 1.0
  Author URI: http://www.innovativephp.com/
*/

spl_autoload_register('wpwa_autoloader');

/*
* Custom autoloader for the application
*
* @param  string Class name
* @return -
*/

$base_path = plugin_dir_path(__FILE__);
require_once $base_path.'/twig/lib/Twig/Autoloader.php';
Twig_Autoloader::register();


function wpwa_autoloader( $class_name ) {

    $class_components = explode( "_", $class_name );
    
    if ( isset( $class_components[0] ) && $class_components[0] == "WPWA" &&
            isset( $class_components[1] )) {

        $class_directory = $class_components[1];

        unset( $class_components[0], $class_components[1] );

        $file_name = implode( "_", $class_components );

        $base_path = plugin_dir_path(__FILE__);

        switch ( $class_directory ) {
            case 'Model':

                $file_path = $base_path . "models/class-wpwa-model-".lcfirst( $file_name ) . '.php';
                if ( file_exists( $file_path ) && is_readable( $file_path ) ) {
                    include $file_path;
                }

                break;

        }
    }
}

/*
 * Managing the initialization of custom post specific functions
*/
class WPWA_Custom_Post_Manager {

    private $base_path;
    private $template_parser;
    private $services;
    private $projects;
    private $books;
    private $articles;

    public function __construct() {

        $this->base_path = plugin_dir_path(__FILE__);

        require_once $this->base_path . 'class-twig-initializer.php';
        $this->template_parser = Twig_Initializer::initialize_templates();


        $this->services     = new WPWA_Model_Service( $this->template_parser );
        $this->projects     = new WPWA_Model_Project( $this->template_parser );
        $this->books        = new WPWA_Model_Book( $this->template_parser );
        $this->articles     = new WPWA_Model_Article( $this->template_parser );

    }

}

$custom_post_manager = new WPWA_Custom_Post_Manager();
?>
