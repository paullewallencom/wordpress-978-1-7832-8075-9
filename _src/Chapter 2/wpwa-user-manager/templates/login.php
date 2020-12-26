<?php get_header(); ?>

<div id='custom_panel'>
    <?php
    if ( count($errors) > 0) {
        foreach ( $errors as $error ) {
            echo "<p class='frm_error'>$error</p>";
        }
    }


    if( isset( $success_message ) && $success_message != ""){
        echo "<p class='frm_success'>$success_message</p>";
    }
    ?>
    <form method='post' action='<?php echo site_url(); ?>/user/login' id='login_form' name='login_form'>
        <ul>
            <li>
                <label class='frm_label' for='username'>Username</label>
                <input class='frm_field' type='text'  name='username' value='<?php echo isset( $username ) ? $username : ''; ?>' />
            </li>
            <li>
                <label class='frm_label' for='password'>Password</label>
                <input class='frm_field' type='password' name='password' value="" />
            </li>
            <li>
                <label class='frm_label' >&nbsp;</label>
                <input  type='submit'  name='submit' value='Login' />
            </li>
        </ul>
    </form>
</div>
<?php get_footer(); ?>
