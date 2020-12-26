Instructions
======================================


Configuring the Plugin
======================
1. wpwa-questions folder contains the actual plugin for question-answer interface
2. copy the plugin to your wp-content/plugins folder of your test server and activate
   through plugin management section.
3. Create questions from the admin area.

Configuring the Theme
=====================

1. Files named archive-wp_questions.php, comments.php, content-questions.php is used for
   setting the theme
2. Activate TwentyTwelve theme from Apperence section and copy archive-wp_questions.php and
   content-questions.php files to the theme folder.
3. Open comments.php file of your theme and replace following code
  
   <?php wp_list_comments( array( 'callback' => 'twentytwelve_comment', 'style' => 'ol' ) ); ?>
   

   with  the following code

   <?php
	if(get_post_type( $post ) == "wp_question") {
                wp_list_comments( array( 'type' => 'comment', 'callback' => 'wpwa_comment_list', 'style' => 'ol' ) );
        }else {
                wp_list_comments( array( 'type' => 'comment', 'callback' => 'twentytwelve_comment', 'style' => 'ol' ) );
        }
   ?>

