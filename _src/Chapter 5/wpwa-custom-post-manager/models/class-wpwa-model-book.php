<?php

/*
 * Manage developer books throughout the portfolio
 * application.
 *
 */

class WPWA_Model_Book {

    private $post_type;
    private $template_parser;
    private $book_category_taxonomy;
    private $error_message;


    /*
     * Execute initiamizations for the books
     *
     * @param  object  Twig Template
     * @return -
    */
    public function __construct( $template_parser ) {
        $this->post_type = "wpwa_book";
        $this->book_category_taxonomy = "wpwa_book_category";


        $this->error_message = "";

        $this->template_parser = $template_parser;

        add_action( 'init', array( $this, 'create_books_post_type' ) );

        add_action( 'init', array( $this, 'create_books_custom_taxonomies' ) );

        add_action( 'add_meta_boxes', array( $this, 'add_books_meta_boxes' ) );

        add_action( 'save_post', array( $this, 'save_book_meta_data' ) );

        add_filter( 'post_updated_messages', array( $this, 'generate_book_messages' ) );

    }

    /*
     * Register custom post type for books
     *
     * @param  -
     * @return -
    */
    public function create_books_post_type() {

        $labels = array(
            'name'                  => __( 'Books', 'wpwa' ),
            'singular_name'         => __( 'Book', 'wpwa' ),
            'add_new'               => __( 'Add New', 'wpwa' ),
            'add_new_item'          => __( 'Add New Book', 'wpwa' ),
            'edit_item'             => __( 'Edit Book', 'wpwa' ),
            'new_item'              => __( 'New Book', 'wpwa' ),
            'all_items'             => __( 'All Book', 'wpwa' ),
            'view_item'             => __( 'View Book', 'wpwa' ),
            'search_items'          => __( 'Search Book', 'wpwa' ),
            'not_found'             => __( 'No Books found', 'wpwa' ),
            'not_found_in_trash'    => __( 'No Books found in the Trash', 'wpwa' ),
            'parent_item_colon'     => '',
            'menu_name'             => __( 'Books', 'wpwa' ),
        );

        $args = array(
            'labels'                => $labels,
            'hierarchical'          => true,
            'description'           => 'Books',
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
     * Register custom taxonomies for the books screen
     *
     * @param  -
     * @return -
    */
    public function create_books_custom_taxonomies() {

        register_taxonomy(
                $this->book_category_taxonomy,
                $this->post_type,
                array(
                    'labels' => array(
                        'name'                  => __( 'Book Category', 'wpwa' ),
                        'singular_name'         => __( 'Book Category', 'wpwa' ),
                        'search_items'          => __( 'Search Book Category', 'wpwa' ),
                        'all_items'             => __( 'All Book Category', 'wpwa' ),
                        'parent_item'           => __( 'Parent Book Category', 'wpwa' ),
                        'parent_item_colon'     => __( 'Parent Book Category:', 'wpwa' ),
                        'edit_item'             => __( 'Edit Book Category', 'wpwa' ),
                        'update_item'           => __( 'Update Book Category', 'wpwa' ),
                        'add_new_item'          => __( 'Add New Book Category', 'wpwa' ),
                        'new_item_name'         => __( 'New Book Category Name', 'wpwa' ),
                        'menu_name'             => __( 'Book Category', 'wpwa' ),
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
    public function add_books_meta_boxes() {
        add_meta_box( "wpwa-books-meta", "Book Details", array( $this, 'display_books_meta_boxes' ), $this->post_type );
    }

    /*
     * Display the custom meta fields for book creation screen
     *
     * @param  -
     * @return -
    */
    public function display_books_meta_boxes() {

        global $post;

        $data = array();
        $data['book_meta_nonce']    = wp_create_nonce("wpwa-book-meta");
        $data['book_url']           = esc_attr(get_post_meta( $post->ID, "_wpwa_book_url", true ));
        $data['book_pages']         = esc_attr(get_post_meta( $post->ID, "_wpwa_book_pages", true ));
        $data['book_publisher']     = esc_attr(get_post_meta( $post->ID, "_wpwa_book_publisher", true ));


        echo $this->template_parser->render( 'book_meta.html', $data );
    }

     /*
     * Save service custom fields to database with neccessary validations
     *
     * @param  -  WordPress generated default messages list
     * @return int  Post ID
    */
    public function save_book_meta_data() {
        global $post;

        // verify nonce
        if ( !wp_verify_nonce($_POST['book_meta_nonce'], "wpwa-book-meta" ) ) {
            return $post->ID;
        }

        // check autosave
        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
            return $post->ID;
        }

        // check permissions
        if ( $this->post_type == $_POST['post_type'] && current_user_can('edit_post', $post->ID ) ) {

            $book_url       = (isset( $_POST['txt_book_url'] ) ? (string) esc_attr( trim($_POST['txt_book_url'] )) : '');
            $book_pages     = (isset( $_POST['txt_book_pages'] ) ? (int) esc_attr( trim($_POST['txt_book_pages'] )) : '');
            $book_publisher = (isset( $_POST['txt_book_publisher'] ) ? (string) esc_attr( trim($_POST['txt_book_publisher'] )) : '');


            if ( empty( $post->post_title ) ) {
                $this->error_message .= __('Book name cannot be empty. <br/>', 'wpwa' );
            }
            if ( empty( $book_pages ) ) {
                $this->error_message .= __('Book pages cannot be empty. <br/>', 'wpwa' );
            }


            if ( !empty( $this->error_message ) ) {
                remove_action( 'save_post', array( $this, 'save_book_meta_data' ) );

                $post->post_status = "draft";
                wp_update_post( $post );

                add_action( 'save_post', array( $this, 'save_book_meta_data' ) );

                $this->error_message = __('Book creation failed.<br/>', 'wpwa') . $this->error_message;

                set_transient( "book_error_message_$post->ID", $this->error_message, 60 * 10 );

            } else {
                update_post_meta( $post->ID, "_wpwa_book_url", $book_url );
                update_post_meta( $post->ID, "_wpwa_book_pages", $book_pages );
                update_post_meta( $post->ID, "_wpwa_book_publisher", $book_publisher );

            }
        } else {
            return $post->ID;
        }
    }

    /*
     * Customize the exising messages for books
     *
     * @param  array  WordPress generated default messages list
     * @return array  Modified messages list
    */
    public function generate_book_messages( $messages ) {
        global $post, $post_ID;

        $this->error_message = get_transient( "book_error_message_$post_$post->ID" );
        $message_no = isset( $_GET['message'] ) ? (int) $_GET['message'] : '0';

        delete_transient( "book_error_message_$post_$post->ID" );

        if ( !empty( $this->error_message ) ) {
            $messages[$this->post_type] = array( "$message_no" => $this->error_message );
        } else {

            $messages[$this->post_type] = array(
                0 => '', // Unused. Messages start at index 1.
                1 => sprintf(__('Book updated. <a href="%s">View Book</a>', 'wpwa' ), esc_url(get_permalink($post_ID))),
                2 => __('Custom field updated.', 'wpwa' ),
                3 => __('Custom field deleted.', 'wpwa' ),
                4 => __('Book updated.', 'wpwa' ),
                5 => isset($_GET['revision']) ? sprintf(__('Book restored to revision from %s', 'wpwa' ), wp_post_revision_title((int) $_GET['revision'], false)) : false,
                6 => sprintf(__('Book published. <a href="%s">View Book</a>', 'wpwa' ), esc_url(get_permalink($post_ID))),
                7 => __('Book saved.', 'wpwa' ),
                8 => sprintf(__('Book submitted. <a target="_blank" href="%s">Preview Book</a>', 'wpwa' ), esc_url(add_query_arg('preview', 'true', get_permalink($post_ID)))),
                9 => sprintf(__('Book scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Book</a>', 'wpwa' ),
                        date_i18n(__('M j, Y @ G:i'), strtotime($post->post_date)), esc_url(get_permalink($post_ID))),
                10 => sprintf(__('Book draft updated. <a target="_blank" href="%s">Preview Book</a>', 'wpwa' ), esc_url(add_query_arg('preview', 'true', get_permalink($post_ID)))),
            );
        }


        return $messages;
    }

}
?>
