<?php

/*
 * Manage developer articles throughout the portfolio
 * application.
 *
 */

class WPWA_Model_Article {

    private $post_type;
    private $template_parser;
    private $article_category_taxonomy;
    private $error_message;


    /*
     * Execute initiamizations for the projects
     *
     * @param  object  Twig Template
     * @return -
    */
    public function __construct( $template_parser ) {
        $this->post_type = "wpwa_article";
        $this->article_category_taxonomy = "wpwa_article_category";

        $this->error_message = "";

        $this->template_parser = $template_parser;

        add_action( 'init', array( $this, 'create_articles_post_type' ) );

        add_action( 'init', array( $this, 'create_articles_custom_taxonomies' ) );

        add_action( 'add_meta_boxes', array( $this, 'add_articles_meta_boxes' ) );

        add_action( 'save_post', array( $this, 'save_article_meta_data' ) );

        add_filter( 'post_updated_messages', array( $this, 'generate_article_messages' ) );

    }


    /*
     * Register custom post type for articles
     *
     * @param  -
     * @return -
    */
    public function create_articles_post_type() {

        $labels = array(
            'name'                  => __( 'Articles', 'wpwa' ),
            'singular_name'         => __( 'Article', 'wpwa' ),
            'add_new'               => __( 'Add New', 'wpwa' ),
            'add_new_item'          => __( 'Add New Article', 'wpwa' ),
            'edit_item'             => __( 'Edit Article', 'wpwa' ),
            'new_item'              => __( 'New Article', 'wpwa' ),
            'all_items'             => __( 'All Articles', 'wpwa' ),
            'view_item'             => __( 'View Article', 'wpwa' ),
            'search_items'          => __( 'Search Articles', 'wpwa' ),
            'not_found'             => __( 'No Articles found', 'wpwa' ),
            'not_found_in_trash'    => __( 'No Articles found in the Trash', 'wpwa' ),
            'parent_item_colon'     => '',
            'menu_name'             => __('Articles', 'wpwa' ),
        );

        $args = array(
            'labels'                => $labels,
            'hierarchical'          => true,
            'description'           => 'Articles',
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
     * Register custom taxonomies for the articles screen
     *
     * @param  -
     * @return -
    */
    public function create_articles_custom_taxonomies() {

        register_taxonomy(
                $this->article_category_taxonomy,
                $this->post_type,
                array(
                    'labels' => array(
                        'name'              => __( 'Article Category', 'wpwa' ),
                        'singular_name'     => __( 'Article Category', 'wpwa' ),
                        'search_items'      => __( 'Search Article Category', 'wpwa' ),
                        'all_items'         => __( 'All Article Category', 'wpwa' ),
                        'parent_item'       => __( 'Parent Article Category', 'wpwa' ),
                        'parent_item_colon' => __( 'Parent Article Category:', 'wpwa' ),
                        'edit_item'         => __( 'Edit Article Category', 'wpwa' ),
                        'update_item'       => __( 'Update Article Category', 'wpwa' ),
                        'add_new_item'      => __( 'Add New Article Category', 'wpwa' ),
                        'new_item_name'     => __( 'New Article Category Name', 'wpwa' ),
                        'menu_name'         => __( 'Article Category', 'wpwa' ),
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
    public function add_articles_meta_boxes() {
        add_meta_box( "wpwa-articles-meta", "Article Details", array( $this, 'display_articles_meta_boxes' ), $this->post_type );
    }

    /*
     * Display the custom meta fields for article creation screen
     *
     * @param  -
     * @return -
    */
    public function display_articles_meta_boxes() {

        global $post;

        $data = array();

        // Get the exisitng values from database
        $data['article_meta_nonce'] = wp_create_nonce("wpwa-article-meta");
        $data['article_url']        = esc_url(get_post_meta( $post->ID, "_wpwa_article_url", true ));

        // Render the twig template by passing the form data
        echo $this->template_parser->render( 'article_meta.html', $data );
    }

     /*
     * Save article custom fields to database with neccessary validations
     *
     * @param  -  WordPress generated default messages list
     * @return int  Post ID
    */
    public function save_article_meta_data() {
        global $post;

        // Verify the nonce value for secure form submission
        if ( !wp_verify_nonce( $_POST['article_meta_nonce'], "wpwa-article-meta" ) ) {
            return $post->ID;
        }

        // Check for the autosaving feature of WordPress
        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
            return $post->ID;
        }

        // Check for Articles post type before executing the saving code
        if ( $this->post_type == $_POST['post_type'] && current_user_can('edit_post', $post->ID ) ) {

            // Retrive the exisiing data from the database
            $aricle_url = (isset( $_POST['article_url'] ) ? (string) esc_url( trim($_POST['article_url'] )) : '');


            // Execute the custom validation checks and define error messages
            if ( empty( $post->post_title ) ) {
                $this->error_message .= __( 'Article name cannot be empty. <br/>', 'wpwa');
            }

            if (empty($aricle_url)) {
                $this->error_message .= __( 'Article URL cannot be empty. <br/>', 'wpwa');
            }


            if ( !empty( $this->error_message ) ) {

                // Process of handling custom validations without running on
                // inifinite loops
                remove_action( 'save_post', array( $this, 'save_article_meta_data' ) );

                $post->post_status = "draft";
                wp_update_post( $post );

                add_action( 'save_post', array( $this, 'save_article_meta_data' ) );

                // Set the error message and temporarly save in the database.
                $this->error_message = __('Article creation failed.<br/>', 'wpwa') . $this->error_message;
                set_transient( "article_error_message_$post->ID", $this->error_message, 60 * 10 );

            } else {

                // Update the custom field values upon successfull validation
                update_post_meta( $post->ID, "_wpwa_article_url", $aricle_url );

            }
        } else {
            return $post->ID;
        }
    }

    /*
     * Customize the exising messages for Articles
     *
     * @param  array  WordPress generated default messages list
     * @return array  Modified messages list
    */
    public function generate_article_messages( $messages ) {
        global $post, $post_ID;

        // Get the temporary error message from database and WordPress generated
        // error no
        $this->error_message = get_transient( "article_error_message_$post->ID" );
        $message_no = isset( $_GET['message'] ) ? (int) $_GET['message'] : '0';

        // Remove the temporary error message from database
        delete_transient( "article_error_message_$post->ID" );

        if ( !empty( $this->error_message ) ) {
            // Override the default WordPress generated message with our own custom
            // message
            $messages[$this->post_type] = array( "$message_no" => $this->error_message );
        } else {

            // Customize the messages list for Articles
            $messages[$this->post_type] = array(
                0 => '', // Unused. Messages start at index 1.
                1 => sprintf(__('Article updated. <a href="%s">View Article</a>', 'wpwa' ), esc_url(get_permalink($post_ID))),
                2 => __('Custom field updated.', 'wpwa' ),
                3 => __('Custom field deleted.', 'wpwa' ),
                4 => __('Article updated.', 'wpwa' ),
                5 => isset($_GET['revision']) ? sprintf(__('Article restored to revision from %s', 'wpwa' ), wp_post_revision_title((int) $_GET['revision'], false)) : false,
                6 => sprintf(__('Article published. <a href="%s">View Article</a>', 'wpwa' ), esc_url(get_permalink($post_ID))),
                7 => __('Article saved.', 'wpwa' ),
                8 => sprintf(__('Article submitted. <a target="_blank" href="%s">Preview Article</a>', 'wpwa' ), esc_url(add_query_arg('preview', 'true', get_permalink($post_ID)))),
                9 => sprintf(__('Article scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Article</a>', 'wpwa' ),
                        date_i18n(__('M j, Y @ G:i'), strtotime($post->post_date)), esc_url(get_permalink($post_ID))),
                10 => sprintf(__('Article draft updated. <a target="_blank" href="%s">Preview Article</a>', 'wpwa' ), esc_url(add_query_arg('preview', 'true', get_permalink($post_ID)))),
            );
        }


        return $messages;
    }

}
?>
