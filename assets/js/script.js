/* This file is coming from SMS */

jQuery(function() {

    let table = new DataTable('#tbl-student-table');

    jQuery("#btn-upload-profile").on("click", function(event) {

        event.preventDefault();

        // Create media instance (Object)
        let mediaUploader = wp.media({
            title: "Select Profile Image",
            multiple: false
        });

        // Select Image Handle Function
        mediaUploader.on("select", function() {

            let attachment = mediaUploader.state().get("selection").first().toJSON();

            //console.log(attachment);

            jQuery("#profile_url").val(attachment.url);
        });

        // Open media modal
        mediaUploader.open();
    });

    // Deactivate event function
    jQuery("#deactivate-student-management-system").on("click", function(event) {

        event.preventDefault();

        var boolenValue = confirm("Are you sure want to deactivate 'Student Management System'?");

        if (boolenValue) {

            window.location.href = jQuery(this).attr("href");
        }


        // Ok -> true, Cancel -> false
    });
});