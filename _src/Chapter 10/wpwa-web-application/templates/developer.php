<?php get_header(); ?>

<div class='main_panel'>
    <div class='developer_profile_panel'>
        <h2>Personal Information</h2>
        <div class='field_label'>Full Name</div>
        <div class='field_value'><?php echo esc_html($data['display_name']); ?></div>
        <div class='field_label'>Country</div>
        <div class='field_value'><?php echo esc_html($data['country']); ?></div>
        <div class='field_label'>Job Role</div>
        <div class='field_value'><?php echo esc_html($data['job_role']); ?></div>
        <div class='field_label'>Skills</div>
        <div class='field_value'><?php echo esc_html($data['skills']); ?></div>
    </div>

    <div id='developer_projects'>
        <h2>Projects</h2>
        <div id="msg_container"></div>
        <?php if ($data['developer_status']) {
 ?>
            <input type='button' id="add_project" value="Add New" />
<?php } ?>
        <div id='pro_add_panel' style='display:none' >
            <div class='field_row'>
                <div class='field_label'>Project Name</div>
                <div class='field_value'><input type='text' id='pro_name' /></div>
            </div>
            <div class='field_row'>
                <div class='field_label'>Status</div>
                <div class='field_value'><select id="pro_status">
                        <option value="0">Select</option>
                        <option value="planned">Planned</option>
                        <option value="pending">Pending</option>
                        <option value="failed">Failed</option>
                        <option value="completed">Completed</option>
                    </select></div>
            </div>
            <div class='field_row'>
                <div class='field_label'>Duration</div>
                <div class='field_value'><input type='text' id='pro_duration' /></div>
            </div>
            <div class='field_row'>
                <div class='field_label'><input type='hidden' id='pro_developer' value='<?php echo $data['developer_id']; ?>' /></div>
                <div class='field_value'><input type='button' id='pro_create' value='Save' /></div>
            </div>
        </div>
        <div >
            <table id='list_projects'>

            </table>
        </div>
    </div>
</div>


<script type="text/template" id="project-list-template">

    <% _.each(projects, function(project) { %>
    <tr class="project_item">
        <td><%= project.get('name') %></td>
        <td><%= project.get('status') %></td>
        <td><%= project.get('duration') %></td>
    </tr>
    <% }); %>

</script>

<script type="text/template" id="project-list-header">

    <tr >
        <th>Project Name</th>
        <th>Status</th>
        <th>Duration</th>
    </tr>

</script>



<?php get_footer(); ?>
