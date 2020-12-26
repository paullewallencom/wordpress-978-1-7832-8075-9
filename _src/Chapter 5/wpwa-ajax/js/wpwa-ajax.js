$jq =jQuery.noConflict();

$jq(document).ready(function() {

    ajaxInitializer({
        "success":"sample_ajax_sucess",
        "data":{
            "name":"John Doe",
            "age":27,
            "action" : wpwa_conf.ajaxActions.sample_key.action,
            "nonce" : wpwa_conf.ajaxNonce
        }
    });

    
    
});

var ajaxInitializer = function(options){

    var defaults = {
        type: 'POST',
        url: wpwa_conf.ajaxURL,
        data: {},
        beforeSend:"",
        success:"",
        error:""
    };

    var settings = $jq.extend({}, defaults, options);

    $jq.ajax(settings);

}

var sample_ajax_sucess = function(data, textStatus, jqXHR){
    console.log(data);
}