<?php

/*
 * Manage developer projects throughout the portfolio
 * application.
 *
*/

class WPWA_Model_Project {

    private $post_type;
    private $template_parser;
    private $technology_taxonomy;
    private $project_type_taxonomy;
    private $error_message;


    /*
     * Execute initiamizations for the projects
     *
     * @param  object  Twig Template
     * @return -
    */
    public function __construct( $template_parser ) {
        $this->post_type                = "wpwa_project";
        $this->technology_taxonomy      = "wpwa_technology";
        $this->project_type_taxonomy    = "wpwa_project_type";

        $this->error_message = "";

        $this->template_parser = $template_parser;

        add_action( 'init', array( $this, 'create_projects_post_type' ) );

        add_action( 'init', array( $this, 'create_projects_custom_taxonomies' ) );

        add_action( 'add_meta_boxes', array( $this, 'add_projects_meta_boxes' ) );

        add_action( 'save_post', array( $this, 'save_project_meta_data' ) );

        add_filter( 'post_updated_messages', array( $this, 'generate_project_messages' ) );

        add_action( 'p2p_init', array( $this, 'join_projects_to_services' ) );
    }


    /*
     * Register custom post type for projects
     *
     * @param  -
     * @return -
    */
    public function create_projects_post_type() {

        $labels = array(
                'name'                  => __( 'Projects', 'wpwa' ),
                'singular_name'         => __( 'Project', 'wpwa' ),
                'add_new'               => __( 'Add New', 'wpwa' ),
                'add_new_item'          => __( 'Add New Project', 'wpwa' ),
                'edit_item'             => __( 'Edit Project', 'wpwa' ),
                'new_item'              => __( 'New Project', 'wpwa' ),
                'all_items'             => __( 'All Projects', 'wpwa' ),
                'view_item'             => __( 'View Project', 'wpwa' ),
                'search_items'          => __( 'Search Projects', 'wpwa' ),
                'not_found'             => __( 'No projects found', 'wpwa' ),
                'not_found_in_trash'    => __( 'No projects found in the Trash', 'wpwa' ),
                'parent_item_colon'     => '',
                'menu_name'             => __( 'Projects', 'wpwa' )
        );

        $args = array(
                'labels'                => $labels,
                'hierarchical'          => true,
                'description'           => 'Projects',
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
     * Register custom taxonomies for the projects screen
     *
     * @param  -
     * @return -
    */
    public function create_projects_custom_taxonomies() {

        register_taxonomy(
                $this->technology_taxonomy,
                $this->post_type,
                array(
                'labels' => array(
                        'name'              => __( 'Technology', 'wpwa' ),
                        'singular_name'     => __( 'Technology', 'wpwa' ),
                        'search_items'      => __( 'Search Technology', 'wpwa' ),
                        'all_items'         => __( 'All Technology', 'wpwa' ),
                        'parent_item'       => __( 'Parent Technology', 'wpwa' ),
                        'parent_item_colon' => __( 'Parent Technology:', 'wpwa' ),
                        'edit_item'         => __( 'Edit Technology', 'wpwa' ),
                        'update_item'       => __( 'Update Technology', 'wpwa' ),
                        'add_new_item'      => __( 'Add New Technology', 'wpwa' ),
                        'new_item_name'     => __( 'New Technology Name', 'wpwa' ),
                        'menu_name'         => __( 'Technology', 'wpwa' ),
                ),
                'hierarchical' => true
                )
        );

        register_taxonomy(
                $this->project_type_taxonomy,
                $this->post_type,
                array(
                'labels' => array(
                        'name'              => __( 'Project Type', 'wpwa' ),
                        'singular_name'     => __( 'Project Type', 'wpwa' ),
                        'search_items'      => __( 'Search Project Type', 'wpwa' ),
                        'all_items'         => __( 'All Project Type', 'wpwa' ),
                        'parent_item'       => __( 'Parent Project Type', 'wpwa' ),
                        'parent_item_colon' => __( 'Parent Project Type:', 'wpwa' ),
                        'edit_item'         => __( 'Edit Project Type', 'wpwa' ),
                        'update_item'       => __( 'Update Project Type', 'wpwa' ),
                        'add_new_item'      => __( 'Add New Project Type', 'wpwa' ),
                        'new_item_name'     => __( 'New Project Type Name', 'wpwa' ),
                        'menu_name'         => __( 'Project Type', 'wpwa' ),
                ),
                'hierarchical' => true,
                'capabilities' => array(
                        'manage_terms'      => 'manage_project_type',
                        'edit_terms'        => 'edit_project_type',
                        'delete_terms'      => 'delete_project_type',
                        'assign_terms'      => 'assign_project_type'
                ),
                )
        );
    }

    /*
     * Define the function for displaying custom meta box
     *
     * @param  -
     * @return -
    */
    public function add_projects_meta_boxes() {
        add_meta_box( 'wpwa-projects-meta', 'Project Details', array( $this, 'display_projects_meta_boxes' ), $this->post_type );
    }

    /*
     * Display the custom meta fields for project creation screen
     *
     * @param  -
     * @return -  
    */
public function display_projects_meta_boxes() {

    global $post;

    $data = array();

    // Get the exisitng values from database
    $data['project_meta_nonce']     = wp_create_nonce("wpwa-project-meta");
    $data['project_url']            = esc_url(get_post_meta( $post->ID, "_wpwa_project_url", true ));
    $data['project_duration']       = esc_attr(get_post_meta( $post->ID, "_wpwa_project_duration", true ));
    $data['project_download_url']   = esc_attr(get_post_meta( $post->ID, "_wpwa_project_download_url", true ));
    $data['project_status']         = esc_attr(get_post_meta( $post->ID, "_wpwa_project_status", true ));
    $data['project_screens']        = json_decode(get_post_meta($post->ID, "_wpwa_project_screens", true));
    // Render the twig template by passing the form data
    echo $this->template_parser->render( 'project_meta.html', $data );
}

    /*
     * Save project custom fields to database with neccessary validations
     *
     * @param  -  WordPress generated default messages list
     * @return int  Post ID
    */
    public function save_project_meta_data() {
        global $post;

        // Verify the nonce value for secure form submission
        if ( !wp_verify_nonce($_POST['project_meta_nonce'], "wpwa-project-meta" ) ) {
            return $post->ID;
        }

        // Check for the autosaving feature of WordPress
        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
            return $post->ID;
        }

        // Check for Projects post type before executing the saving code
        if ( $this->post_type == $_POST['post_type'] && current_user_can( 'edit_post', $post->ID ) ) {

            // Retrive the exisiing data from the database
            $project_url            = (isset( $_POST['txt_url'] ) ? (string) esc_url( trim($_POST['txt_url']) ) : '');
            $project_duration       = (isset( $_POST['txt_duration'] ) ? (float) esc_attr( trim($_POST['txt_duration'] )) : '');
            $project_download_url   = (isset( $_POST['txt_download_url'] ) ? (string) esc_attr( trim($_POST['txt_download_url'] )) : '');
            $project_status         = (isset( $_POST['sel_project_status'] ) ? (string) esc_attr( trim($_POST['sel_project_status'] )) : '');

            // Execute the custom validation checks and define error messages
            if ( empty( $post->post_title ) ) {
                $this->error_message .= __('Project name cannot be empty. <br/>', 'wpwa' );
            }
            if ( '0' == $project_status ) {
                $this->error_message .= __('Project status cannot be empty. <br/>', 'wpwa' );
            }
            if ( empty( $project_duration ) ) {
                $this->error_message .= __('Project duration cannot be empty. <br/>', 'wpwa' );
            }


            if ( !empty( $this->error_message ) ) {

                // Process of handling custom validations without running on
                // inifinite loops
                remove_action( 'save_post', array( $this, 'save_project_meta_data' ) );

                $post->post_status = "draft";
                wp_update_post( $post );

                add_action( 'save_post', array( $this, 'save_project_meta_data' ) );

                // Set the error message and temporarly save in the database.
                $this->error_message = __('Project creation failed.<br/>', 'wpwa' ) . $this->error_message;
                set_transient( "project_error_message_$post->ID", $this->error_message, 60 * 10 );

            } else {

                // Update the custom field values upon successfull validation
                update_post_meta( $post->ID, "_wpwa_project_url", $project_url );
                update_post_meta( $post->ID, "_wpwa_project_duration", $project_duration );
                update_post_meta( $post->ID, "_wpwa_project_download_url", $project_download_url );
                update_post_meta( $post->ID, "_wpwa_project_status", $project_status );

                $project_screens = isset ($_POST['h_project_screens']) ? $_POST['h_project_screens'] : "";
                $project_screens = json_encode($project_screens);
                update_post_meta($post->ID, "_wpwa_project_screens", $project_screens);
            }
        } else {
            return $post->ID;
        }
    }

    /*
     * Customize the exising messages for Projects
     *
     * @param  array  WordPress generated default messages list
     * @return array  Modified messages list
    */
    public function generate_project_messages( $messages ) {
        global $post, $post_ID;

        // Get the temporary error message from database and WordPress generated
        // error no
        $this->error_message = get_transient( "project_error_message_$post->ID" );
        $message_no = isset($_GET['message']) ? (int) $_GET['message'] : '0';

        // Remove the temporary error message from database
        delete_transient( "project_error_message_$post->ID" );

        if ( !empty( $this->error_message ) ) {
            // Override the default WordPress generated message with our own custom
            // message
            $messages[$this->post_type] = array( "$message_no" => $this->error_message );
        } else {

            // Customize the messages list for Projects
            $messages[$this->post_type] = array(
                    0 => '', // Unused. Messages start at index 1.
                    1 => sprintf(__('Project updated. <a href="%s">View Project</a>', 'wpwa' ), esc_url(get_permalink($post_ID))),
                    2 => __('Custom field updated.', 'wpwa' ),
                    3 => __('Custom field deleted.', 'wpwa' ),
                    4 => __('Project updated.', 'wpwa' ),
                    5 => isset($_GET['revision']) ? sprintf(__('Project restored to revision from %s', 'wpwa' ), wp_post_revision_title((int) $_GET['revision'], false)) : false,
                    6 => sprintf(__('Project published. <a href="%s">View Project</a>', 'wpwa' ), esc_url(get_permalink($post_ID))),
                    7 => __('Project saved.', 'wpwa' ),
                    8 => sprintf(__('Project submitted. <a target="_blank" href="%s">Preview Project</a>', 'wpwa' ), esc_url(add_query_arg('preview', 'true', get_permalink($post_ID)))),
                    9 => sprintf(__('Project scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Project</a>', 'wpwa' ),
                    date_i18n(__('M j, Y @ G:i'), strtotime($post->post_date)), esc_url(get_permalink($post_ID))),
                    10 => sprintf(__('Project draft updated. <a target="_blank" href="%s">Preview Project</a>', 'wpwa' ), esc_url(add_query_arg('preview', 'true', get_permalink($post_ID)))),
            );
        }


        return $messages;
    }

    /*
     * Register a relatioshhip type between Projects and
     * Services using the Posts 2 Posts plugin
     *
     * @param  -
     * @return -
    */
    public function join_projects_to_services() {

        p2p_register_connection_type( array(
                'name'  => 'projects_to_services',
                'from'  => $this->post_type,
                'to'    => 'wpwa_service'
                ) );

    }
    
}
?>
