jQuery(function() {

    console.log("Welcome to Front-end Js File");

    jQuery("#btn_shortcode_form").on("click", function(event) {

        event.preventDefault();

        var formdata = jQuery("#frm-frontend").serialize() + "&action=sms_ajax_handler&param=frontend_form";

        jQuery.ajax({
            url: sms_ajax_url,
            data: formdata,
            method: "POST",
            success: function(response) {

                console.log(response)
            },
            error: function() {

            }
        });
    });
});