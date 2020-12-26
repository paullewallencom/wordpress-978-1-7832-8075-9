<?php

/*
  Plugin Name: WPWA Theme Manager
  Plugin URI:
  Description: Theme management module for the portfolio management application.
  Author: Rakhitha Nimesh
  Version: 1.0
  Author URI: http://www.innovativephp.com/
 */

class WPWA_Theme {

    private $templates;

    /*
     * Configuring and intializing theme files and actions
     *
     * @param  -
     * @return -
     */

    public function __construct() {

        add_action('template_redirect', array($this, 'application_controller'));
        add_action('widgets_init', array($this, 'register_widgets'));

        add_action('wp_enqueue_scripts', array($this, 'include_styles'));
        $this->register_widget_areas();
        $this->template_init();

        add_action('wpwa_home_widgets_controls', array($this, 'home_widgets_controls'), 10, 2);
    }

    /*
     * Register widgetized areas
     *
     * @param  -
     * @return -
     */

    public function register_widget_areas() {

        register_sidebar(array(
            'name' => __('Home Widgets','wpwa'),
            'id' => 'home-widgets',
            'description' => __('Home Widget Area', 'wpwa'),
            'before_widget' => '<div id="one" class="home_list">',
            'after_widget' => '</div>',
            'before_title' => '<h2>',
            'after_title' => '</h2>'
        ));
    }

    /*
     * Include the widget classes and register the widgets
     *
     * @param  -
     * @return -
     */

    public function register_widgets() {
        $base_path = plugin_dir_path(__FILE__);
        include $base_path . 'widgets.php';
        register_widget('Home_List_Widget');
    }

    /*
     * Include a custom template loader
     *
     * @param  -
     * @return -
     */

    public function template_init() {
        include_once 'class-wpwa-template-loader.php';
    }

    /*
     * Control the default template loading process with custom templates
     *
     * @param  -
     * @return -
     */

    public function application_controller() {
        global $wp_query;
        $control_action = isset ( $wp_query->query_vars['control_action'] ) ? $wp_query->query_vars['control_action'] : '';

        if (is_home () && empty($control_action) ) {
            $tmpl = new WPWA_Template_Loader();
            $tmpl->render("home");
            exit;
        }
    }

    /*
     * Include styles and scripts for the plugin
     *
     * @param  -
     * @return -
     */

    public function include_styles() {
        wp_register_style('wpwa_theme', plugins_url('css/wpwa-theme.css', __FILE__));
        wp_enqueue_style('wpwa_theme');
    }

         /*
     * Adding dynamic controls into extendable areas
     *
     * @param  string   $type   Type of widget to add controls
     * @param  int      $id     Database table records Id
     * @return -
     */
    public function home_widgets_controls($type, $id) {

        if ($type == 'follow') {
            echo "<input type='button' class='$type' id='" . $type . "_" . $id . "' data-id='$id' value='" . ucfirst($type) . "' />";
        }
    }

}

$wpwa_theme = new WPWA_Theme();
?>
