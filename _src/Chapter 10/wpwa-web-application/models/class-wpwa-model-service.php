<?php

/*
 * Manage developer services throughout the portfolio
 * application.
 *
 */

class WPWA_Model_Service {

    private $post_type;
    private $template_parser;
    private $service_tasks_taxonomy;
    private $error_message;


    /*
     * Execute initiamizations for the services
     *
     * @param  object  Twig Template
     * @return -
    */
    public function __construct( $template_parser ) {
        $this->post_type = 'wpwa_service';
        $this->service_tasks_taxonomy = "wpwa_service_tasks";

        $this->error_message = "";

        $this->template_parser = $template_parser;

        add_action( 'init', array( $this, 'create_services_post_type' ) );

        add_action( 'init', array( $this, 'create_services_custom_taxonomies' ) );

        add_action( 'add_meta_boxes', array( $this, 'add_services_meta_boxes' ) );

        add_action( 'save_post', array( $this, 'save_service_meta_data' ) );

        add_filter( 'post_updated_messages', array( $this, 'generate_service_messages' ) );

    }


    /*
     * Register custom post type for services
     *
     * @param  -
     * @return -
    */
    public function create_services_post_type() {

        $labels = array(
            'name'                  => __( 'Services', 'wpwa' ),
            'singular_name'         => __( 'Service', 'wpwa' ),
            'add_new'               => __( 'Add New', 'wpwa' ),
            'add_new_item'          => __( 'Add New Service', 'wpwa' ),
            'edit_item'             => __( 'Edit Service', 'wpwa' ),
            'new_item'              => __( 'New Service', 'wpwa' ),
            'all_items'             => __( 'All Services', 'wpwa' ),
            'view_item'             => __( 'View Service', 'wpwa' ),
            'search_items'          => __( 'Search Services', 'wpwa' ),
            'not_found'             => __( 'No Services found', 'wpwa' ),
            'not_found_in_trash'    => __( 'No Services found in the Trash', 'wpwa' ),
            'parent_item_colon'     => '',
            'menu_name'             => __( 'Services', 'wpwa' ),
        );

        $args = array(
            'labels'                => $labels,
            'hierarchical'          => true,
            'description'           => 'Services',
            'supports'              => array('title', 'editor'),
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'show_in_nav_menus'     => true,
            'publicly_queryable'    => true,
            'exclude_from_search'   => false,
            'has_archive'           => true,
            'query_var'             => true,
            'can_export'            => true,
            'rewrite'               => true,
            'capability_type'       => 'post',

        );

        register_post_type( $this->post_type, $args );
    }

    /*
     * Register custom taxonomies for the services screen
     *
     * @param  -
     * @return -
    */
    public function create_services_custom_taxonomies() {

        register_taxonomy(
                $this->service_tasks_taxonomy,
                $this->post_type,
                array(
                    'labels' => array(
                        'name'              => __( 'Service Tasks', 'wpwa' ),
                        'singular_name'     => __( 'Service Tasks', 'wpwa' ),
                        'search_items'      => __( 'Search Service Tasks', 'wpwa' ),
                        'all_items'         => __( 'All Service Tasks', 'wpwa' ),
                        'parent_item'       => __( 'Parent Service Task', 'wpwa' ),
                        'parent_item_colon' => __( 'Parent Service Tasks:', 'wpwa' ),
                        'edit_item'         => __( 'Edit Service Tasks', 'wpwa' ),
                        'update_item'       => __( 'Update Service Tasks', 'wpwa' ),
                        'add_new_item'      => __( 'Add New Service Tasks', 'wpwa' ),
                        'new_item_name'     => __( 'New Service Tasks Name', 'wpwa' ),
                        'menu_name'         => __ ('Service Tasks', 'wpwa' ),
                    ),
                    'hierarchical' => true,
                )
        );


    }

    /*
     * Define the function for displaying custom meta box
     *
     * @param  -
     * @return -
    */
    public function add_services_meta_boxes() {
        add_meta_box( "wpwa-services-meta", "Service Details", array( $this, 'display_services_meta_boxes' ), $this->post_type );
    }

    /*
     * Display the custom meta fields for service creation screen
     *
     * @param  -
     * @return -
    */
    public function display_services_meta_boxes() {

        global $post;

        $data = array();

        // Get the exisitng values from database
        $data['service_meta_nonce']     = wp_create_nonce("wpwa-service-meta");
        $data['service_price_type']     = esc_attr(get_post_meta($post->ID, "_wpwa_service_price_type", true));
        $data['service_price_value']    = esc_attr(get_post_meta($post->ID, "_wpwa_service_price_value", true));
        $data['service_availability']   = esc_attr(get_post_meta($post->ID, "_wpwa_service_availability", true));


        // Render the twig template by passing the form data
        echo $this->template_parser->render( 'service_meta.html', $data );
    }

     /*
     * Save service custom fields to database with neccessary validations
     *
     * @param  -  WordPress generated default messages list
     * @return int  Post ID
    */
    public function save_service_meta_data() {
        global $post;

        // Verify the nonce value for secure form submission
        if ( !wp_verify_nonce($_POST['service_meta_nonce'], "wpwa-service-meta" ) ) {
            return $post->ID;
        }

        // Check for the autosaving feature of WordPress
        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
            return $post->ID;
        }

        // Check for Services post type before executing the saving code
        if ( $this->post_type == $_POST['post_type'] && current_user_can( 'edit_post', $post->ID ) ) {

            // Retrive the exisiing data from the database
            $service_price_type     = (isset( $_POST['service_price_type'] ) ? (string) esc_attr( trim($_POST['service_price_type'] )) : '');
            $service_price_value    = (isset( $_POST['service_price_value'] ) ? (float) esc_attr( trim($_POST['service_price_value'] )) : '');
            $service_availability   = (isset( $_POST['service_availability'] ) ? (string) esc_attr( trim($_POST['service_availability'] )) : '');

            // Execute the custom validation checks and define error messages

            if ( empty( $post->post_title ) ) {
                $this->error_message .= __('Service name cannot be empty. <br/>', 'wpwa');
            }
            if ( '0' == $service_availability) {
                $this->error_message .= __('Service availability cannot be empty. <br/>' , 'wpwa');
            }

            if ( !empty( $this->error_message ) ) {

                // Process of handling custom validations without running on
                // inifinite loops
                remove_action('save_post', array( $this, 'save_service_meta_data' ));

                $post->post_status = "draft";
                wp_update_post( $post );

                add_action( 'save_post', array( $this, 'save_service_meta_data' ) );

                // Set the error message and temporarly save in the database.
                $this->error_message = __('Service creation failed.<br/>', 'wpwa') . $this->error_message;
                set_transient( "service_error_message_$post->ID", $this->error_message, 60 * 10 );

            } else {

                // Update the custom field values upon successfull validation
                update_post_meta( $post->ID, "_wpwa_service_price_type", $service_price_type );
                update_post_meta( $post->ID, "_wpwa_service_price_value", $service_price_value );
                update_post_meta( $post->ID, "_wpwa_service_availability", $service_availability );

            }
        } else {
            return $post->ID;
        }
    }

    /*
     * Customize the exising messages for Services
     *
     * @param  array  WordPress generated default messages list
     * @return array  Modified messages list
    */
    public function generate_service_messages( $messages ) {
        global $post, $post_ID;

        // Get the temporary error message from database and WordPress generated
        // error no
        $this->error_message = get_transient( "service_error_message_$post->ID" );
        $message_no = isset( $_GET['message'] ) ? (int) $_GET['message'] : '0';

        // Remove the temporary error message from database
        delete_transient( "service_error_message_$post->ID" );

        if ( !empty( $this->error_message ) ) {
            // Override the default WordPress generated message with our own custom
            // message
            $messages[$this->post_type] = array( "$message_no" => $this->error_message );
        } else {

            // Customize the messages list for Services
            $messages[$this->post_type] = array(
                0 => '', // Unused. Messages start at index 1.
                1 => sprintf(__('Service updated. <a href="%s">View Service</a>', 'wpwa' ), esc_url(get_permalink($post_ID))),
                2 => __('Custom field updated.', 'wpwa' ),
                3 => __('Custom field deleted.', 'wpwa' ),
                4 => __('Service updated.', 'wpwa' ),
                5 => isset($_GET['revision']) ? sprintf(__('Service restored to revision from %s', 'wpwa' ), wp_post_revision_title((int) $_GET['revision'], false)) : false,
                6 => sprintf(__('Service published. <a href="%s">View Service</a>', 'wpwa' ), esc_url(get_permalink($post_ID))),
                7 => __('Service saved.', 'wpwa' ),
                8 => sprintf(__('Service submitted. <a target="_blank" href="%s">Preview Service</a>', 'wpwa' ), esc_url(add_query_arg('preview', 'true', get_permalink($post_ID)))),
                9 => sprintf(__('Service scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Service</a>', 'wpwa' ),
                        date_i18n(__('M j, Y @ G:i'), strtotime($post->post_date)), esc_url(get_permalink($post_ID))),
                10 => sprintf(__('Service draft updated. <a target="_blank" href="%s">Preview Service</a>', 'wpwa' ), esc_url(add_query_arg('preview', 'true', get_permalink($post_ID)))),
            );
        }


        return $messages;
    }

}
?>
