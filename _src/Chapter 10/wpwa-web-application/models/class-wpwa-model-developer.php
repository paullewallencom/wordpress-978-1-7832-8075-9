<?php

/*
 * Manage developers throughout the portfolio
 * application.
 *
 */

class WPWA_Model_Developer {

    public function list_developers() {
        $user_query = new WP_User_Query(array('role' => 'developer', 'number' => 25));
        return $user_query->results;       
    }
}
?>
