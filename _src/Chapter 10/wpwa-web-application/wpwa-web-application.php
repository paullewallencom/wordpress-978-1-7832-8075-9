<?php

/*
  Plugin Name: WPWA Web Application
  Plugin URI: http://www.innovativephp.com/
  Description: Building a Portfolio Management System to illustrate the power of WordPress as a
  web application development framework
  Author: Rakhitha Nimesh
  Version: 1.0
  Author URI: http://www.innovativephp.com/
 */

require_once 'wpwa-list-table/class-wpwa-list-table.php';
//require_once 'wpwa-admin-theme/class-wpwa-admin-theme.php';
require_once 'wpwa-ajax/class-wpwa-ajax.php';
require_once 'wpwa-custom-post-manager/class-wpwa-custom-post-manager.php';
require_once 'wpwa-dashboard/class-wpwa-dashboard.php';
require_once 'wpwa-database-manager/class-wpwa-database-manager.php';
require_once 'wpwa-file-uploader/class-wpwa-file-uploader.php';
require_once 'wpwa-opauth/class-wpwa-opauth.php';
require_once 'wpwa-open-source/class-wpwa-open-source.php';
require_once 'wpwa-pluggable-plugin/wpwa-pluggable-plugin.php';
require_once 'wpwa-theme/class-wpwa-theme.php';
require_once 'wpwa-user-manager/class-wpwa-user-manager.php';
require_once 'wpwa-xml-rpc-api/class-wpwa-xml-rpc-api.php';


include_once 'class-wpwa-template-loader.php';

include_once 'utility.php';


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

function wpwa_autoloader($class_name) {

    $class_components = explode("_", $class_name);
    if (isset($class_components[0]) && $class_components[0] == "WPWA" &&
            isset($class_components[1])) {

        $class_directory = $class_components[1];

        unset($class_components[0],$class_components[1]);

        $file_name = implode("_", $class_components);

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

class WPWA_Web_Application {

    public function initialize_controllers() {

        require_once 'controllers/class-template-router.php';
        $template_router = new Template_Router();
        $template_router->redirect_templates();

        require_once 'controllers/class-activation-controller.php';
        $activation_controller = new Activation_Controller();
        $activation_controller->initialize_activation_hooks();

        require_once 'controllers/class-script-controller.php';
        $script_controller = new Script_Controller();
        $script_controller->enque_scripts();

        require_once 'controllers/class-admin-menu-controller.php';
        $admin_menu_controller = new Admin_Menu_Controller();
        $admin_menu_controller->initialize_admin_menu();
    }

    public function initialize_app_controllers() {

        $xml_rpc = new WPWA_XML_RPC_API();
        $xml_rpc->initialize();

        $user_manager = new WPWA_User_Manager();
        $user_manager->initialize();

        $app_theme = new WPWA_Theme();
        $app_theme->initialize();

        $open_source = new WPWA_Open_source();
        $open_source->initialize();

        $opauth = new WPWA_Opauth();
        $opauth->initialize();

        $file_uploader = new WPWA_File_Uploader();
        $file_uploader->initialize();

        $dashboard = new WPWA_Dashboard();
        $dashboard->initialize();
        //$dashboard->set_frontend_toolbar(FALSE);

        $ajax = new WPWA_AJAX();
        $ajax->initialize();

        $base_path = plugin_dir_path(__FILE__);
        require_once $base_path . 'class-twig-initializer.php';

        $custom_posts = new WPWA_Custom_Post_Manager();
        $custom_posts->initialize();
    }

}

$wpwa_web_app = new WPWA_Web_Application();
$wpwa_web_app->initialize_controllers();
$wpwa_web_app->initialize_app_controllers();



