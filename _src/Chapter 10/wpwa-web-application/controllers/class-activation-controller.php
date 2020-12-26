<?php

class Activation_Controller {

    public function initialize_activation_hooks() {
        register_activation_hook("wpwa-web-application/wpwa-web-application.php", array($this, 'execute_activation_hooks'));
    }

    public function execute_activation_hooks() {
        wp_schedule_event(time(), 'everytenminutes', 'notification_sender');


        $database_manager = new WPWA_Database_Manager();
        $database_manager->create_custom_tables();

        $user_manager = new WPWA_User_Manager();
        $user_manager->add_application_user_roles();
        $user_manager->remove_application_user_roles();
        $user_manager->add_application_user_capabilities();

        $template_router = new Template_Router();
        $template_router->flush_rewriting_rules();

        
    }


}
?>