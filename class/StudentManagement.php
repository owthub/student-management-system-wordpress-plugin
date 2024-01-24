<?php

class StudentManagement{

     private $message = "";
     private $status = "";
     private $action = "";

     // Constructor
     public function __construct(){

        add_action("admin_menu", array($this, "addAdminMenus"));

        add_action("admin_enqueue_scripts", array($this, "addStudentPluginFiles"));

        add_filter("plugin_action_links_".SMS_PLUGIN_BASENAME, array($this, "plugin_settings_link"));

        // plugin_action_links_{plugin_folder_name/plugin_main_file}

        add_action("admin_init", array($this, "sms_plugin_settings"));
     }
     
     // Add Settings link
     public function plugin_settings_link($links){

        $settings_link = '<a href="options-general.php?page=sms-plugin-settings">Settings</a>';
        array_unshift($links, $settings_link);
        
        return $links;
     }
     
     // Add Student Plugin Menus and Submenus
     public function addAdminMenus(){

        // Plugin menu
        add_menu_page("Student System", "Student System", "manage_options", "student-system", array($this, "listStudentCallback"), "dashicons-admin-home");

        // Plugin submenu
        add_submenu_page("student-system", "List Student", "List Student", "manage_options", "student-system", array($this, "listStudentCallback"));

        // Plugin submenu
        add_submenu_page("student-system", "Add Student", "Add Student", "manage_options", "add-student", array($this, "addStudentCallback"));

        // Setttings page submenu inside Settings Menu
       // add_options_page("SMS Plugin Settings", "SMS Plugin Settings", "manage_options", "sms-plugin-settings", array($this, "sms_plugin_action_handle"));

      add_submenu_page("edit.php", "SMS Plugin Settings", "SMS Plugin Settings", "manage_options", "sms-plugin-settings",array($this, "sms_plugin_action_handle") );
     }

     // plugin action handler
     public function sms_plugin_action_handle(){
        echo "<h3>SMS Plugin Settings<h3>";
        include_once SMS_PLUGIN_PATH.'pages/sms-plugin-settings.php';
     }

     // Plugin Settings
     public function sms_plugin_settings(){
      
      add_settings_section("form_validation_rule_section_1", "Form Rule Validation Settings", "", "sms-plugin-settings");

      // Name field
      register_setting("sms_plugin_options", "sms_name_validation");
      add_settings_field("sms_name_field", "Name Field Validation", array($this, "sms_name_field_handle"), "sms-plugin-settings", "form_validation_rule_section_1");


      // Email field
      register_setting("sms_plugin_options", "sms_email_validation");
      add_settings_field("sms_email_field", "Email Field Validation", array($this, "sms_email_field_handle"), "sms-plugin-settings", "form_validation_rule_section_1");

      // Gender field
      register_setting("sms_plugin_options", "sms_gender_validation");
      add_settings_field("sms_gender_field", "Gender Field Validation", array($this, "sms_gender_field_handle"), "sms-plugin-settings", "form_validation_rule_section_1");

      // Phone field
      register_setting("sms_plugin_options", "sms_phone_validation");
      add_settings_field("sms_phone_field", "Phone Field Validation", array($this, "sms_phone_field_handle"), "sms-plugin-settings", "form_validation_rule_section_1");
     }

     public function sms_name_field_handle(){

        $saved_value = get_option("sms_name_validation");

        $checked = "";

        if(!empty($saved_value)){

          $checked = "checked";
        }

        echo '<input type="checkbox" name="sms_name_validation" value="1" '.$checked.'/>';
     }

     public function sms_email_field_handle(){

      $saved_value = get_option("sms_email_validation");

        $checked = "";

        if(!empty($saved_value)){

          $checked = "checked";
        }

         echo '<input type="checkbox" name="sms_email_validation" value="1" '.$checked.'/>';
     }

     public function sms_gender_field_handle(){

      $saved_value = get_option("sms_gender_validation");

        $checked = "";

        if(!empty($saved_value)){

          $checked = "checked";
        }

         echo '<input type="checkbox" name="sms_gender_validation" value="1" '.$checked.'/>';
    }

  public function sms_phone_field_handle(){

   $saved_value = get_option("sms_phone_validation");

        $checked = "";

        if(!empty($saved_value)){

          $checked = "checked";
        }

      echo '<input type="checkbox" name="sms_phone_validation" value="1" '.$checked.'/>';
   }

     // List Student call back
     public function listStudentCallback(){

      // Action and ID
      if($_GET['action'] == "edit"){

         global $wpdb;

         $this->action = "edit";

         $student_id = $_GET['id'];
         $table_prefix = $wpdb->prefix;

         if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['btn_submit'])){

            $name = sanitize_text_field($_POST['name']);
            $email = sanitize_text_field($_POST['email']);
            $gender = sanitize_text_field($_POST['gender']);
            $phone = sanitize_text_field($_POST['phone']);

            $wpdb->update("{$table_prefix}student_system", array(
               "name" => $name,
               "email" => $email,
               "gender" => $gender,
               "phone_no" => $phone
            ), array(
               "id" => $student_id
               ));

               $this->message = "Student updated successfully";
         }

         $student = $this->getStudentData($student_id);
         $displayMessage = $this->message;
         $action = $this->action;

         include_once SMS_PLUGIN_PATH .'pages/add-student.php';
      }elseif($_GET['action'] == "view"){

         $this->action = "view";
         $student_id = $_GET['id'];

         $action = $this->action;
         $student = $this->getStudentData($student_id);
         // View Single Student data
         include_once SMS_PLUGIN_PATH .'pages/add-student.php'; 
      }else{

         global $wpdb;

         $table_prefix = $wpdb->prefix; // wp_

         if($_GET['action'] == "delete"){

            $data = $this->getStudentData(intval($_GET['id']));

            if(!empty($data)){

               // Delete function
               $wpdb->delete("{$table_prefix}student_system", array(
                  "id" => intval($_GET['id'])
               ));

               $this->message = "Student deleted successfully";
            }else{

               // no action
            }
         }

         $students = $wpdb->get_results("SELECT * FROM {$table_prefix}student_system", ARRAY_A);

         $displayMessage = $this->message;
         //echo "<h3>Welcome to this list student page</h3>";
         include_once SMS_PLUGIN_PATH. 'pages/list-student.php';
      }
     }

     // Return Student Data
     private function getStudentData($student_id){

      global $wpdb;
      $table_prefix = $wpdb->prefix;

      $student = $wpdb->get_row(
         $wpdb->prepare("SELECT * FROM {$table_prefix}student_system WHERE id = %d", $student_id), ARRAY_A
      );

      return $student;

     }

     // Add Student call back
     public function addStudentCallback(){

        // Form Submission Code
        if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['btn_submit'])){

           // NONCE Verification
           if(isset($_POST['wp_nonce_add_student']) && wp_verify_nonce($_POST['wp_nonce_add_student'], "wp_nonce_add_student")){

              // Success
              $this->saveStudentFormData();
           }else{

              // Failed
              $this->message = "Verification failed";
              $this->status = 0;
           }
        }

        $displayMessage = $this->message;
        $displayStatus = $this->status;

        //echo "<h3>Welcome to this add student page</h3>";
        include_once SMS_PLUGIN_PATH .'pages/add-student.php';
     }

    // Save Student Form data
    private function saveStudentFormData(){

      global $wpdb;

      $name = sanitize_text_field($_POST['name']);
      $email = sanitize_text_field($_POST['email']);
      $gender = sanitize_text_field($_POST['gender']);
      $phone = sanitize_text_field($_POST['phone']);
      $profile_url = sanitize_text_field($_POST['profile_url']);

      $table_prefix = $wpdb->prefix; // wp_

      $wpdb->insert("{$table_prefix}student_system", array(

         "name" => $name,
         "email" => $email,
         "gender" => $gender,
         "phone_no" => $phone,
         "profile_image" => $profile_url
      ));

      $student_id = $wpdb->insert_id;

      if($student_id > 0){

         // data save
         $this->message = "Student saved successfully";
         $this->status = 1;
      }else{

         // failed to save data
         $this->message = "Failed to save data";
         $this->status = 0;
      }
    }

     // Create Student Table
     public function createStudentTable(){

        global $wpdb;

        $prefix = $wpdb->prefix; // wp_

        $sql = '
        CREATE TABLE `'.$prefix.'student_system` (
            `id` int NOT NULL AUTO_INCREMENT,
            `name` varchar(50) NOT NULL,
            `email` varchar(80) NOT NULL,
            `gender` enum("male","female","other") DEFAULT NULL,
            `phone_no` varchar(25) DEFAULT NULL,
            `profile_image` TEXT,
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
           ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
        ';

        include_once ABSPATH. 'wp-admin/includes/upgrade.php';

        dbDelta($sql);
     }

     // Drop Student Table
     public function dropStudentTable(){

        global $wpdb;

        $prefix = $wpdb->prefix; // wp_

        $sql = "DROP TABLE IF EXISTS ".$prefix."student_system";

        $wpdb->query($sql);
     }

     // Add Plugin File
     public function addStudentPluginFiles(){

        // Style
        wp_enqueue_style("datatable-css", SMS_PLUGIN_URL . "assets/css/jquery.dataTables.min.css", array(), "1.0", "all");
        wp_enqueue_style("custom-css", SMS_PLUGIN_URL . "assets/css/custom.css", array(), "1.0", "all");

        // Script
        wp_enqueue_media();
        wp_enqueue_script("datatable-js", SMS_PLUGIN_URL . "assets/js/jquery.dataTables.min.js", array("jquery"), "1.0");
        wp_enqueue_script("script-js", SMS_PLUGIN_URL . "assets/js/script.js", array("jquery"), "1.0");
     }

}