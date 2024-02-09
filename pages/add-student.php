
<div class="sms-plugin card">
    <h2>
        <?php 
            if(isset($action) && $action == "edit"){
                echo "Edit Student";
            }elseif(isset($action) && $action == "view"){
                echo "View Student";
            }else{
            
                echo "Add Student";
            }

            $nonce = wp_create_nonce("wp_nonce_add_student");
        ?>
    </h2>

    <?php if(!empty($displayMessage) && $displayStatus){
        ?>  
        <div class="display-success">
           <?php echo $displayMessage; ?>
        </div>
        <?php
    } ?>

    <?php if(!empty($displayMessage) && !$displayStatus ){
        ?>  
        <div class="display-error">
           <?php echo $displayMessage; ?>
        </div>
        <?php
    } ?>
    
    <form class="add-student-form" id="frm_sms_form" method="post" action="javascript:void(0);">

        <input type="hidden" name="wp_nonce_add_student" value="<?php echo $nonce; ?>">

        <?php
        if(isset($action) && $action == "edit"){
          ?>
            <input type="hidden" name="operation_type" value="edit"> 
            <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>"> 
          <?php
        }else{
            ?>
            <input type="hidden" name="operation_type" value="add">
            <?php
        }
        ?>

        <!-- Name -->
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" <?php if(isset($action) && $action == "view") { echo 'readonly'; } ?> value="<?php if(isset($student['name'])){ echo $student['name']; } ?>" name="name" id="name" placeholder="Enter name" <?php
                    $saved_name_value = get_option("sms_name_validation");

                    if(!empty($saved_name_value)){

                        echo "required";
                    }
            ?>>
        </div>

        <!-- Email -->
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" <?php if(isset($action) && $action == "view") { echo 'readonly'; } ?> value="<?php if(isset($student['email'])){ echo $student['email']; } ?>" name="email" id="email" placeholder="Enter email" <?php 
                 
                 $saved_email_value = get_option("sms_email_validation");

                 if(!empty($saved_email_value)){

                    echo "required";
                 }
            ?>>
        </div>


        <!-- Gender -->
        <div class="form-group">
            <label for="gender">Gender</label>
            <select name="gender" id="gender" <?php if(isset($action) && $action == "view") { echo 'disabled'; } ?> <?php 

                    $saved_gender_value = get_option("sms_gender_validation");

                    if(!empty($saved_gender_value)){

                        echo "required";
                    }
            ?> >
                <option value="">Select Gender</option>
                <option <?php if(isset($student['gender']) && $student['gender'] == "male") { echo 'selected'; } ?> value="male">Male</option>
                <option <?php if(isset($student['gender']) && $student['gender'] == "female") { echo 'selected'; } ?> value="female">Female</option>
                <option <?php if(isset($student['gender']) && $student['gender'] == "other") { echo 'selected'; } ?> value="other">Other</option>
            </select>
        </div>

        <!-- Phone no -->
        <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="text" <?php if(isset($action) && $action == "view") { echo 'readonly'; } ?> value="<?php if(isset($student['phone_no'])){ echo $student['phone_no']; } ?>" name="phone" id="phone" placeholder="Enter phone" <?php
               $saved_phone_value = get_option("sms_phone_validation");

               if(!empty($saved_phone_value)){

                echo "required";
               }
            ?>>
        </div>

        <!-- Bio -->
        <div class="form-group">
            <label for="phone">Bio Description</label>
            <?php 
            $content = isset($student['profile_bio']) && !empty($student['profile_bio']) ? $student['profile_bio'] : "";
            $editor_id = "sms_bio_editor";
            $args = array(
                'tinymce'       => array(
                    'toolbar1'      => 'bold,italic,underline,separator,alignleft,aligncenter,alignright,separator,link,unlink,undo,redo',
                    'toolbar2'      => '',
                    'toolbar3'      => '',
                ),
            );
            wp_editor( $content, $editor_id, $args );
            ?>
        </div>

        <!-- Upload Button -->
        <input type="text" style="margin-bottom: 5px;" name="profile_url" id="profile_url" readonly>

        <button id="btn-upload-profile" style="margin-bottom: 13px;
    background: #0000ffa3;">Upload Profile Image</button>

        <?php 
        if(isset($action) && $action == "view"){
                // No submit button
        }else{
            ?>
                <button type="submit" id="btn_sms_form" name="btn_submit">Submit</button>
            <?php
        }
        ?>
    
    </form>
</div>