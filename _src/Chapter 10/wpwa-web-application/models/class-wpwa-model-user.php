<?php

/*
 * Manage users throughout the portfolio
 * application.
 *
 */

class WPWA_Model_User {

public function add_profile_fields() {
    global $user_ID;

    $job_role = esc_html(get_user_meta($user_ID, "_wpwa_job_role", TRUE));
    $skills = esc_html(get_user_meta($user_ID, "_wpwa_skills", TRUE));
    $country = esc_html(get_user_meta($user_ID, "_wpwa_country", TRUE));

    $tmp = new WPWA_Template_Loader();
    $tmp->render("profile_fields", array("job_role"=>$job_role,"skills"=>$skills,"country"=>$country));
}

    public function save_profile_fields() {
        global $user_ID;
   
        $job_role = isset($_POST['job_role']) ? esc_html(trim($_POST['job_role'])) : "";
        $skills = isset($_POST['skills']) ? esc_html(trim($_POST['skills'])) : "";
        $country = isset($_POST['country']) ? esc_html(trim($_POST['country'])) : "";

        update_user_meta($user_ID, "_wpwa_job_role", $job_role);
        update_user_meta($user_ID, "_wpwa_skills", $skills);
        update_user_meta($user_ID, "_wpwa_country", $country);
    }

}
?>
