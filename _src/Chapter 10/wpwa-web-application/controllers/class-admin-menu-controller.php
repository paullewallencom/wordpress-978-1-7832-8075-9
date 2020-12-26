<?php

class Admin_Menu_Controller{

    public function initialize_admin_menu(){
        add_action('admin_menu', array($this, 'execute_admin_menu'));
    }

    public function execute_admin_menu(){

        $xml_rpc = new WPWA_XML_RPC_API();
        $xml_rpc->api_settings();

        $dashboard = new WPWA_Dashboard();
        $dashboard->customize_main_navigation();
    }
}
