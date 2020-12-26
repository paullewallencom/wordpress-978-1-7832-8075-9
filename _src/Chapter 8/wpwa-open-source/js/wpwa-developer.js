$jq =jQuery.noConflict();


$jq(document).ready(function(){

    var Project =  Backbone.Model.extend({
        defaults: {
            name: '',
            status: '',
            duration:'',
            developerId : ''
        },

        validate: function(attrs) {           
            var errors = this.errors = {};

            if (!attrs.name) errors.name = 'Project name is required';
            if (attrs.status == 0) errors.status = 'Status is required';
            if (!attrs.duration) errors.duration = 'Duration is required';

            if (!_.isEmpty(errors)){
                console.log(errors);
                return errors;
            }
        }
    });

    var ProjectCollection = Backbone.Collection.extend({
        model: Project,
        url: wpwaScriptData.ajaxUrl+"?action=wpwa_process_projects&developer_id="+wpwaScriptData.developerID
    });

    var projectsList;
    var ProjectListView = Backbone.View.extend({
        el: $jq('#developer_projects'),

        initialize: function () {
            projectsList = new ProjectCollection();
            projectsList.bind("change", _.bind(this.getData, this));
            this.getData();

        },
        getData: function () {
            var obj = this;
            projectsList.fetch({
                success: function () {
                    obj.render();
                }
            });
        },
        render: function () {
            var template_data = _.template($jq('#project-list-template').html(), {
                projects: projectsList.models
            });
     
            var header_data = $jq('#project-list-header').html();

            $jq(this.el).find("#list_projects").html(header_data+template_data);
            return this;
        },
        events: {
            'click #add_project': 'addNewProject',
            'click #pro_create': 'saveNewProject'
        },

        addNewProject: function(event) {
            $jq("#pro_add_panel").show();
           
        },
        saveNewProject: function(event) {
            var options = {
                success: function (response) {
                    if("error" == response.changed.status){
                        console.log(response.changed.msg)
                    }
                },
                error: function (model, error) {
                    
                    console.log(error);
                }
            };

            var project = new Project();

            var name = $jq("#pro_name").val();
            var duration = $jq("#pro_duration").val();
            var status = $jq("#pro_status").val();
            var developerId = $jq("#pro_developer").val();
    
            projectsList.add(project);

            projectsList.create({
                name: name,
                duration:duration,
                status : status,
                developerId : developerId
            },options);

        }
    });

    var projectView  = new ProjectListView();

});



