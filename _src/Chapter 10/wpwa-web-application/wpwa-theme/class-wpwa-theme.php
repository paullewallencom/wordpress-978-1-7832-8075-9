<?php

class WPWA_Theme {

    private $templates;

    /*
     * Configuring and intializing theme files and actions
     *
     * @param  -
     * @return -
     */

    public function __construct() {

        //add_action('template_redirect', array($this, 'wpwa_application_controller'));
        //add_action('widgets_init', array($this, 'wpwa_register_widgets'));
        //add_action('wp_enqueue_scripts', array($this, 'wpwa_include_styles'));
        //$this->wpwa_register_widget_areas();
        //$this->wpwa_template_init();
        //add_action('home_widgets_controls', array($this, 'wpwa_home_widgets_controls'), 10, 2);
    }

    public function initialize() {

        add_action('widgets_init', array($this, 'register_widgets'));


        $this->register_widget_areas();


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
            'name' => __('Home Widgets', 'wpwa'),
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
     * Control the default template loading process with custom templates
     *
     * @param  -
     * @return -
     */

    public function application_controller() {
        global $wp_query;
        $control_action = isset($wp_query->query_vars['control_action']) ? $wp_query->query_vars['control_action'] : '';


        if (is_home () && empty($control_action)) {
            $tmpl = new WPWA_Template_Loader();
            $tmpl->render("home");
            exit;
        }
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

//$wpwa_theme = new WPWA_Theme();
?>
