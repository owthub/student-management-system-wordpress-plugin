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

    // Form submit code
    jQuery("#btn_sms_form").on("click", function(event) {

        event.preventDefault();

        var editorContent = tinymce.get("sms_bio_editor").getContent();

        var formData = jQuery("#frm_sms_form").serialize() +
            "&action=sms_ajax_handler&param=save_form&bio=" + editorContent;

        // Ajax request 
        jQuery.ajax({
            url: sms_ajax_url,
            data: formData,
            method: "POST",
            success: function(response) {
                // Success response
                var data = jQuery.parseJSON(response);

                if (data.status) {

                    toastr.success(data.message);

                    setTimeout(function() {
                        location.reload()
                    }, 2000);
                } else {

                    toastr.error(data.message);

                    setTimeout(function() {
                        location.reload()
                    }, 2000);
                }
            },
            error: function(response) {
                // Error
            }
        })
    });

    if (jQuery("#tbl-student-table").length > 0) {

        load_students();
    }
});

function load_students() {

    var formData = "action=sms_ajax_handler&param=load_students";

    var studentHTML = "";

    jQuery("#tbl-student-table").DataTable().destroy();

    jQuery.ajax({
        url: sms_ajax_url,
        data: formData,
        method: "GET",
        success: function(response) {

            var data = jQuery.parseJSON(response);

            //console.log(data);

            if (data.status) {

                // We have students
                jQuery.each(data.data, function(index, student) {

                    studentHTML += "<tr>";
                    studentHTML += "<td>" + student.id + "</td>";
                    studentHTML += "<td>" + student.name + "</td>";
                    studentHTML += "<td>" + student.email + "</td>";
                    studentHTML += '<td><img style="height:100px;width:100px" src="' + student.profile_image + '"/></td>';
                    studentHTML += "<td>" + student.gender + "</td>";
                    studentHTML += "<td>" + student.phone_no + "</td>";
                    studentHTML += '<td><a href="admin.php?page=student-system&action=edit&id=' + student.id + '" class="btn-edit">Edit</a> <a class="btn-view" href="admin.php?page=student-system&action=view&id=' + student.id + '">View</a> <a class="btn-delete btn-student-delete" data-id="' + student.id + '">Delete</a></td>';
                    studentHTML += "</tr>";
                });

                jQuery("#tbl-student-table tbody").html(studentHTML);

                jQuery("#tbl-student-table").DataTable();
            }
        },
        error: function() {

        }
    });

    // Delete function
    jQuery(document).on("click", ".btn-student-delete", function() {

        if (confirm("Are you sure want to delete?")) { // true

            var student_id = jQuery(this).attr("data-id");

            var formData = "action=sms_ajax_handler&param=delete_student&student_id=" + student_id;

            jQuery.ajax({
                url: sms_ajax_url,
                data: formData,
                method: "POST",
                success: function(response) {

                    var data = jQuery.parseJSON(response);

                    toastr.success(data.message);

                    setTimeout(function() {
                        location.reload()
                    }, 3000);
                },
                error: function() {

                }
            });
        }
    });
}