
<div class="sms-plugin card">
    <h2>
        <?php 
            if(isset($action) && $action == "edit"){
                echo "Edit Student";
            }elseif(isset($action) && $action == "view"){
                echo "View Student";
            }else{
                
                $nonce = wp_create_nonce("wp_nonce_add_student");

                echo "Add Student";
            }
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
    
    <form class="add-student-form" method="post" <?php if($action == "edit") { 
        ?> action="admin.php?page=student-system&action=edit&id=<?php echo $student['id'] ?>" <?php
     }else{
        ?>
         action="admin.php?page=add-student"
        <?php 
    } ?>>

        <input type="hidden" name="wp_nonce_add_student" value="<?php echo $nonce; ?>">

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

        <!-- Upload Button -->
        <input type="text" style="margin-bottom: 5px;" name="profile_url" id="profile_url" readonly>

        <button id="btn-upload-profile" style="margin-bottom: 13px;
    background: #0000ffa3;">Upload Profile Image</button>

        <?php 
        if(isset($action) && $action == "view"){
                // No submit button
        }else{
            ?>
                <button type="submit" name="btn_submit">Submit</button>
            <?php
        }
        ?>
    
    </form>
</div>