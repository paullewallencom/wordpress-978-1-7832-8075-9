<?php

class WPWA_Database_Manager {

    public function __construct() {
        //register_activation_hook(__FILE__,array($this,'create_custom_tables' ));
    }

    public function create_custom_tables() {
        global $wpdb;
        $table_name = $wpdb->prefix . "user_activities";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        $sql = "CREATE TABLE $table_name (
  			id mediumint(9) NOT NULL AUTO_INCREMENT,
  			time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
  			user_id mediumint(9) NOT NULL,
  			activity text NOT NULL,
  			url VARCHAR(255) DEFAULT '' NOT NULL,
  			UNIQUE KEY id (id)
			);";

        dbDelta($sql);


        $table_name = $wpdb->prefix . "subscribed_developers";


        $sql = "CREATE TABLE $table_name (
                    id mediumint(9) NOT NULL AUTO_INCREMENT,
                    follower_id mediumint(9) NOT NULL,
                    developer_id mediumint(9) NOT NULL,
                    UNIQUE KEY id (id)
                    );";

        dbDelta($sql);
    }

}

//$database_manager = new WPWA_Database_Manager();
