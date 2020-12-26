<?php
class Home_List_Widget extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    public function __construct() {
        parent::__construct(
                        'home_list_widget', // Base ID
                        'Home_List_Widget', // Name
                        array('description' => __('Home List Widget', 'wpwa'),) // Args
        );
    }

    /**
     * Front-end display of widget.
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget($args, $instance) {
        extract($args);
        $title = apply_filters('widget_title', $instance['title']);
        $list_type = apply_filters('widget_list_type', $instance['list_type']);

        echo $before_widget;
        if (!empty($title))
            echo $before_title . $title . $after_title;


        $tmp = new WPWA_Template_Loader();


        switch ($list_type) {
            case 'dev':
                // Get list of developers from the database
                $user_query = new WP_User_Query(array('role' => 'developer', 'number' => 10));
                $data = array();
                $data["records"] = array();
                foreach ($user_query->results as $developer) {
                    array_push($data["records"], array("ID" => $developer->data->ID, "title" => $developer->data->display_name, "type"=>"follow"));
                }
                $data["title"] = $title;
                $tmp->render("home_list", $data);
                break;

            case 'fol':
                // Get list of followers from the database
                $user_query = new WP_User_Query(array('role' => 'follower', 'number' => 10));
                $data = array();
                $data["records"] = array();
                foreach ($user_query->results as $follower) {
                    array_push($data["records"], array("ID" => $follower->data->ID, "title" => $follower->data->display_name, "type"=>""));
                }
                $data["title"] = $title;
                $tmp->render("home_list", $data);
                break;


            case 'pro':
                // Get list of projects from the database
                $projects = new WP_Query(array('post_type' => 'wpwa_project', 'post_status' => 'publish', 'posts_per_page' => 5));
                $data = array();
                $data["records"] = array();


                if ($projects->have_posts()) : while ($projects->have_posts()) : $projects->the_post();
                        array_push($data["records"], array("ID" => get_the_ID(), "title" => get_the_title(), "type"=>""));

                    endwhile;
                endif;

                wp_reset_query();

                $data["title"] = $title;
                $tmp->render("home_list", $data);


                break;
        }



        echo $after_widget;
    }

    /**
     * Back-end widget form.
     * @param array $instance Previously saved values from database.
     */
    public function form($instance) {
        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = __('New title', 'wpwa');
        }

        if (isset($instance['list_type'])) {
            $list_type = $instance['list_type'];
        } else {
            $list_type = 0;
        }
?>
        <p>
            <label for="<?php echo $this->get_field_name('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>


        <p>
            <label for="<?php echo $this->get_field_name('list_type'); ?>"><?php _e('List Type:'); ?></label>
    <?php echo $list_type; ?>
        <select class="widefat" id="<?php echo $this->get_field_id('list_type'); ?>" name="<?php echo $this->get_field_name('list_type'); ?>" >
            <option <?php selected( $list_type, 0 ); ?>  value='0'>Select</option>
            <option <?php selected( $list_type, "dev" ); ?>  value='dev'>Latest Developers</option>
            <option <?php selected( $list_type, "pro" ); ?>  value='pro'>Latest Projects</option>
            <option <?php selected( $list_type, "fol" ); ?>  value='fol'>Latest Followers</option>
        </select>
    </p>
<?php
    }

    /**
     * Sanitize widget form values as they are saved.

     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
        $instance['list_type'] = (!empty($new_instance['list_type']) ) ? strip_tags($new_instance['list_type']) : '';

        return $instance;
    }

}

?>
