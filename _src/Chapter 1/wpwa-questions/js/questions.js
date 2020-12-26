$jq =jQuery.noConflict();

$jq(document).ready( function() {
    
    $jq(".answer-status").click( function() {

        // Get the button object and current status of the answer
        var answer_button = $jq(this);
        var answer_status  = $jq(this).attr("data-ques-status");

        // Get the ID of the clicked answer using hidden field
        var comment_id = $jq(this).parent().find(".hcomment").val();
        var data = {
            "comment_id":comment_id,
            "status": answer_status
        };

        // Create the AJAX request to save the status to database
        $jq.post( wpwaconf.ajaxURL, {
            action:"mark_answer_status",
            nonce:wpwaconf.ajaxNonce,
            data : data,
        }, function( data ) {
            if("success" == data.status){
                if("valid" == answer_status){
                    $jq(answer_button).val("Mark as Incorrect");
                    $jq(answer_button).attr("data-ques-status","invalid");
                }else{
                    $jq(answer_button).val("Mark as Correct");
                    $jq(answer_button).attr("data-ques-status","valid");
                }
            }
        }, "json");
    });

});


