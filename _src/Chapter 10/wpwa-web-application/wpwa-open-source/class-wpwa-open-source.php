<?php

class WPWA_Open_source {

    public function __construct() {
        //register_activation_hook(__FILE__, array($this, 'flush_application_rewrite_rules'));
        //add_action('init', array($this, 'manage_routes'));
        //add_filter('query_vars', array($this, 'manage_routes_query_vars'));
        //add_action('new_to_publish', array($this, 'send_subscriber_notifictions'));
        //add_action('draft_to_publish', array($this, 'send_subscriber_notifictions'));
        //add_action('pending_to_publish', array($this, 'send_subscriber_notifictions'));
        //add_action('wp_enqueue_scripts', array($this, 'include_scripts'));
        //$this->wpwa_template_init();
        //add_action('wp_ajax_nopriv_process_projects', array($this, 'process_projects'));
        //add_action('wp_ajax_process_projects', array($this, 'process_projects'));
        //add_action('template_redirect', array($this, 'front_controller'));
    }

    public function initialize() {
        add_action('new_to_publish', array($this, 'send_subscriber_notifications'));
        add_action('draft_to_publish', array($this, 'send_subscriber_notifications'));
        add_action('pending_to_publish', array($this, 'send_subscriber_notifications'));

//        $this->wpwa_template_init();

        add_action('wp_ajax_nopriv_wpwa_process_projects', array($this, 'process_projects'));
        add_action('wp_ajax_wpwa_process_projects', array($this, 'process_projects'));
    }

    public function process_projects() {

        $request_data = json_decode(file_get_contents("php://input"));

        $project_developer = isset ($_GET['developer_id']) ? $_GET['developer_id'] : '0';

        if (is_object($request_data) && isset($request_data->name)) {

            $project_name = $request_data->name;
            $project_status = $request_data->status;
            $project_duration = $request_data->duration;
            $project_developer = $request_data->developerID;

            $err = FALSE;
            $err_message = '';

            if ($project_name == '') {
                $err = TRUE;
                $err_message .= 'Project name is required.';
            }
            if ($project_status == '0') {
                $err = TRUE;
                $err_message .= 'Status is required.';
            }
            if ($project_duration == '') {
                $err = TRUE;
                $err_message .= 'Duration is required.';
            }

            if ($err) {
                echo json_encode(array('status'=>'error', 'msg'=> $err_message));
                exit;
            } else {

                $current_user = wp_get_current_user();

                $post_details = array(
                    'post_title' => esc_html($project_name),
                    'post_status' => 'publish',
                    'post_type' => 'wpwa_project',
                    'post_author' => $current_user->ID
                );

                $result = wp_insert_post($post_details);
                if (is_wp_error($result)) {
                    echo json_encode(array('status'=>'error', 'msg'=> $result));
                } else {
                    update_post_meta($result, "_wpwa_project_status", esc_html($project_status));
                    update_post_meta($result, "_wpwa_project_duration", esc_html($project_duration));

                    echo json_encode(array('status'=>'success'));
                }
            }
            exit;
        }else{

            $result = $this->list_projects($project_developer);
                echo json_encode($result);
                exit;
        }
    }

    

    

    public function list_projects($developer_id) {
        $projects = new WP_Query(array('author' => $developer_id, 'post_type' => 'wpwa_project', 'post_status' => 'publish',
                    'posts_per_page' => 15, 'orderby' => 'date'));
        $data = array();


        if ($projects->have_posts()) : while ($projects->have_posts()) : $projects->the_post();

                $post_id = get_the_ID();
                $status = get_post_meta($post_id, '_wpwa_project_status', TRUE);
                $duration = get_post_meta($post_id, '_wpwa_project_duration', TRUE);
                array_push($data, array("ID" => $post_id, "name" => get_the_title(), "status" => $status,
                    "duration" => $duration));

            endwhile;
        endif;

        return $data;
    }

    public function create_developer_profile($developer_id) {

        $user_query = new WP_User_Query(array('include' => array($developer_id)));

        $data = array();
        foreach ($user_query->results as $developer) {
            $data['display_name'] = $developer->data->display_name;
            $data['job_role'] = esc_html(get_user_meta($developer->data->ID, "_wpwa_job_role", TRUE));
            $data['skills'] = esc_html(get_user_meta($developer->data->ID, "_wpwa_skills", TRUE));
            $data['country'] = esc_html(get_user_meta($developer->data->ID, "_wpwa_country", TRUE));
        }

        $current_user = wp_get_current_user();

        $data['developer_status'] = ($current_user->ID == $developer_id);
        $data['developer_id'] = $developer_id;

        $tmp = new WPWA_Template_Loader();
        $tmp->render("developer", $data);
        exit;
    }

    
    

    public function send_subscriber_notifications($post) {

        update_post_meta($post->ID, "notify_status", "0");


//        global $post, $wpdb;
//
//
//        if ('wpwa_book' == $_POST['post_type'] || 'wpwa_project' == $_POST['post_type']
//                || 'wpwa_services' == $_POST['post_type'] || 'wpwa_article' == $_POST['post_type']) {
//
//            require_once ABSPATH . WPINC . '/class-phpmailer.php';
//
//            require_once ABSPATH . WPINC . '/class-smtp.php';
//            $phpmailer = new PHPMailer(true);
//
//            $phpmailer->From = "example.com";
//            $phpmailer->FromName = "Portfolio Application";
//
//            $phpmailer->SMTPAuth = true;
//            $phpmailer->IsSMTP(); // telling the class to use SMTP
//            $phpmailer->Host = "ssl://smtp.gmail.com"; // SMTP server
//            $phpmailer->Username = "example@gmail.com";
//            $phpmailer->Password = "passowrd";
//            $phpmailer->Port = 465;
//
//            $phpmailer->Subject = "New Activity on Portfolio Application";
//
//
//            $sql = "SELECT user_nicename,user_email
//                                FROM $wpdb->users
//                                INNER JOIN " . $wpdb->prefix . "subscribed_developers
//                                ON " . $wpdb->users . ".ID = " . $wpdb->prefix . "subscribed_developers.follower_id
//                                WHERE " . $wpdb->prefix . "subscribed_developers.developer_id = '$post->post_author'
//                            ";
//
//
//            $subscribers = $wpdb->get_results($sql);
//
//            foreach ($subscribers as $subscriber) {
//                $phpmailer->AddBcc($subscriber->user_email, $subscriber->user_nicename);
//            }
//
//            $phpmailer->Body = "New Update from your favorite developers " . get_permalink($post->ID);
//            $phpmailer->Send();
//        } else {
//            return;
//        }
    }

}

//$open_source = new WPWA_Open_source();

