<?php get_header(); ?>


<div id="custom_panel">
    <?php
    if(count($data['errors']) > 0){
    foreach ($data['errors'] as $error) {
        echo "<div class='frm_error'>".$error."</div>";
    }
    }
    ?>

<form id="registration-form" method="post" action="<?php echo get_site_url() . '/user/register'; ?>">
    <ul>
        <li>
            <label class="frm_label" for="Username">Username</label>
            <input class="frm_field" type="text" id="username" name="user" value='<?php echo isset( $data['user_login'] ) ? $data['user_login'] : ''; ?>' />
        </li>
        <li>
            <label class="frm_label" for="Email">E-mail</label>
            <input class="frm_field" type="text" id="email" name="email" value='<?php echo isset( $data['user_email'] ) ? $data['user_email'] : ''; ?>' />
        </li>
        <li>
            <label class="frm_label" for="User Type">User Type</label>
            <select class='frm_field' name='user_type'>
                    <option <?php echo (isset( $data['user_type'] ) && $data['user_type'] == 'follower') ? 'selected' : ''; ?> value='follower'>Follower</option>
                    <option <?php echo (isset( $data['user_type'] ) && $data['user_type'] == 'developer') ? 'selected' : ''; ?> value='developer'>Developer</option>
                    <option <?php echo (isset( $data['user_type'] ) && $data['user_type'] == 'member') ? 'selected' : ''; ?> value='member'>Member</option>
                </select>
        </li>
        <li>
            <label class="frm_label" for="">&nbsp;</label>
            <input type="submit" value="Register" />
        </li>
    </ul>
</form>
</div>

<?php get_footer(); ?>
