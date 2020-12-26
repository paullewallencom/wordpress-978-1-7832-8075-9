<?php

class Template_Router {

    public function redirect_templates() {
        add_action('template_redirect', array($this, 'main_router'));

        add_filter('query_vars', array($this, 'manage_routes_query_vars'));

        add_action('init', array($this, 'manage_routing_rules'));
    }

    public function main_router() {

        $opauth = new WPWA_Opauth();
        $opauth->load_opauth();

        $this->front_controller();

        $app_theme = new WPWA_Theme();
        $app_theme->application_controller();
    }

    public function manage_routes_query_vars($query_vars) {
        $query_vars[] = 'control_action';
        $query_vars[] = 'record_id';

        return $query_vars;
    }

    public function manage_routing_rules() {

        add_rewrite_rule('^user/([^/]+)/([^/]+)/?', 'index.php?control_action=$matches[1]&record_id=$matches[2]', 'top');
        add_rewrite_rule('^user/([^/]+)/?', 'index.php?control_action=$matches[1]', 'top');
        add_rewrite_rule('^list/([^/]+)/?', 'index.php?control_action=$matches[1]', 'top');
    }

    public function flush_rewriting_rules() {
        $this->manage_routing_rules();
        flush_rewrite_rules();
    }

    public function front_controller() {
        global $wp_query;
        $control_action = $wp_query->query_vars['control_action'];

        switch ($control_action) {
            case 'register':
                do_action('wpwa_register_user');
                break;

            case 'login':
                do_action('wpwa_login_user');
                break;

            case 'activate':
                do_action('wpwa_activate_user');
                break;

            case 'profile':
                $developer_id = $wp_query->query_vars['record_id'];
                $app_theme = new WPWA_Open_source();
                $app_theme->create_developer_profile($developer_id);
                break;

            case 'developers':

                $developer = new WPWA_Model_Developer();
                $result = $developer->list_developers();

                $tmp = new WPWA_Template_Loader();
                $tmp->render("developer_list", array("developers" => $result));
                exit;
                break;
        }
    }

}