<?php

/*
  Plugin Name: WPWA Pluggable Plugin
  Plugin URI:
  Description: Explain the use of pluggable plugins by sending mails on post saving
  Version: 1.0
  Author: Rakhitha Nimesh
  Author URI: http://www.innovativephp.com/
  License: GPLv2 or later
 */

add_action( 'save_post', 'wpwa_create_newsletter' );

function wpwa_create_newsletter( $post_id ) {

    if ( !wp_is_post_revision( $post_id ) ) {

        $post_title = esc_html(get_the_title( $post_id ) );
        $post_url = esc_url(get_permalink( $post_id ) );


        wpwa_send_newletter( $post_title, $post_url, "projects" );
    }
}


add_filter( 'wp_mail_content_type', 'wpwa_mail_content_type' );
function wpwa_mail_content_type() {
    return 'text/html';
}


if ( !function_exists( 'wpwa_send_newletter' ) ) {

    function wpwa_send_newletter($heading, $content) {

        $message = "<p><b>$heading</b><br/></p>";
        $message .= "<p>$content<br/></p>";

        wp_mail( "example@example.com", "Pluggable Plugins", $message );
    }

}


function wpwa_send_newletter( $heading, $content , $template_name = "") {

        $message = "";


	if(empty( $template_name ) ){
		$message = "<p><b>$heading</b><br/></p>";
        	$message .= "<p>$content<br/></p>";
	}else{

		$template = wpwa_get_template($template_name);
		$message .= str_replace("%title%",$heading,$template);
		$message   = str_replace("%content%",$content,$message);

	}
        

        wp_mail("example@example.com", "Pluggable Plugins", $message);
}


function wpwa_get_template( $template_name ) {
	$template = "";
	switch( $template_name ){
		case 'projects':
			$template .= "<h2>%title%</h2><br/><p><i>%content%</i></p>";
			break;
	}

	return $template;
}
