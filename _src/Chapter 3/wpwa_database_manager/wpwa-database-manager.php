<?php
/*
  Plugin Name: WPWA Database Manager
  Plugin URI:
  Description: Database management module for the portfolio management application.
  Author: Rakhitha Nimesh
  Version: 1.0
  Author URI: http://www.innovativephp.com/
*/

class WPWA_Database_Manager {

    public function __construct(){
            register_activation_hook( __FILE__, array( $this, 'create_custom_tables' ) );
    }

    public function create_custom_tables() {
            global $wpdb;
            $table_name = $wpdb->prefix.user_activities;

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

            $sql = "CREATE TABLE $table_name (
                    id mediumint(9) NOT NULL AUTO_INCREMENT,
                    time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                    user_id mediumint(9) NOT NULL,
                    activity text NOT NULL,
                    url VARCHAR(255) DEFAULT '' NOT NULL,
                    UNIQUE KEY id (id)
                    );";

            dbDelta( $sql );

            // subscribed_developers will be created in a similar manner

    }
}

$database_manager = new WPWA_Database_Manager();
