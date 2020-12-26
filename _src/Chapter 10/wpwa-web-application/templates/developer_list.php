<?php get_header(); ?>
 
<div class='main_panel'>
    <div class='developer_profile_panel'>
        <h2>Developer List</h2>
        <div class='field_label'><input type="text" id="autocomplete_dev_list" name="autocomplete_dev_list" /></div>
    </div>

    <div id='developer_list'>
        <?php foreach($data['developers'] as $developer){ ?>
        <div class="developer_row"><a href="<?php echo site_url();?>/user/profile/<?php echo $developer->data->ID; ?>"><?php echo esc_html($developer->data->display_name);?></a></div>
        <?php } ?>
    </div>
</div>


<?php get_footer(); ?>
