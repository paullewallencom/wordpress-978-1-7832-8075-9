<?php

class WPWA_Dashboard {
    /*
     * Include neccessary actions and filters to initialize the plugin.
     *
     * @param  -
     * @return -
     */

    public function __construct() {
        //add_action('wp_before_admin_bar_render', array($this, 'wpwa_customize_admin_toolbar'));
        //add_action('admin_menu', array($this, 'wpwa_customize_main_navigation'));
    }

    public function initialize() {
        add_action('wp_before_admin_bar_render', array($this, 'customize_admin_toolbar'));
    }

    /*
     * Enable or disable front end admin toolbar
     *
     * @param  boolean $status Display status of admin toolbar
     * @return -
     */

    public function set_frontend_toolbar($status) {
        show_admin_bar($status);
    }

    /*
     * Customize exisitng menu items and adding new menu items
     *
     * @param  - 
     * @return -
     */

    public function customize_admin_toolbar() {
        global $wp_admin_bar;

        $wp_admin_bar->remove_menu('updates');
        $wp_admin_bar->remove_menu('comments');
        $wp_admin_bar->remove_menu('new-content');

        if (current_user_can('edit_posts')) {
            $wp_admin_bar->add_menu(array(
                'id' => 'wpwa-developers',
                'title' => 'Developer Components',
                'href' => admin_url()
            ));

            $wp_admin_bar->add_menu(array(
                'id' => 'wpwa-new-books',
                'title' => 'Books',
                'href' => admin_url() . "post-new.php?post_type=wpwa_book",
                'parent' => 'wpwa-developers'
            ));

            $wp_admin_bar->add_menu(array(
                'id' => 'wpwa-new-projects',
                'title' => 'Projects',
                'href' => admin_url() . "post-new.php?post_type=wpwa_project",
                'parent' => 'wpwa-developers'
            ));
        }
    }

    /*
     * Removes the dashboard menu item
     *
     * @param  - 
     * @return -
     */

    public function customize_main_navigation() {
        global $menu, $submenu;
        //unset($menu[2]);
    }

}

//$admin_dashboard = new WPWA_Dashboard();
//$admin_dashboard->set_frontend_toolbar(FALSE);
?>
