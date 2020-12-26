<?php

function everytenminutes($schedules) {

    $schedules['everytenminutes'] = array(
        'interval' => 60,
        'display' => __('Once Ten Minutes')
    );
    return $schedules;
}

add_filter('cron_schedules', 'everytenminutes');

add_action("notification_sender", "notification_send");

function notification_send() {
    global $wpdb;

    require_once ABSPATH . WPINC . '/class-phpmailer.php';

    require_once ABSPATH . WPINC . '/class-smtp.php';

    $phpmailer = new PHPMailer(true);

    $phpmailer->From = "example@gmail.com";
    $phpmailer->FromName = "Portfolio Application";

    $phpmailer->SMTPAuth = true;
    $phpmailer->IsSMTP(); // telling the class to use SMTP
    $phpmailer->Host = "ssl://smtp.gmail.com"; // SMTP server
    $phpmailer->Username = "example@gmail.com";
    $phpmailer->Password = "password";
    $phpmailer->Port = 465;
    $phpmailer->IsHTML(true);

    $phpmailer->Subject = "New Schedule";

    $args = array(
        'post_type' => array('wpwa_service', 'wpwa_book', 'wpwa_project', 'wpwa_article'),
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => 'notify_status',
                'value' => '0'
            )
        )
    );
    $post_query = null;
    $post_query = new WP_Query($args);

    $message = "";

    if ($post_query->have_posts()) : while ($post_query->have_posts()) : $post_query->the_post();

            $author = get_the_author_ID();
            $sql = "SELECT user_nicename,user_email
                                FROM $wpdb->users
                                INNER JOIN " . $wpdb->prefix . "subscribed_developers
                                ON " . $wpdb->users . ".ID = " . $wpdb->prefix . "subscribed_developers.follower_id
                                WHERE " . $wpdb->prefix . "subscribed_developers.developer_id = '$author'
                            ";


            $subscribers = $wpdb->get_results($sql);

            $message.= "<a href='" . get_permalink() . "'>" . get_the_title() . "</a>";

            foreach ($subscribers as $subscriber) {
                $phpmailer->AddBcc($subscriber->user_email, $subscriber->user_nicename);
            }

            $phpmailer->Body = "New Updates from your favorite developers<br/><br/><br/>." . $message;
            $phpmailer->Send();

            update_post_meta(get_the_ID(), "notify_status", "1");


        endwhile;
    endif;
}


?>
