<?php

class StudentManagement{

     private $message = "";
     private $status = "";
     private $action = "";

     // Constructor
     public function __construct(){

        add_action("admin_menu", array($this, "addAdminMenus"));

        add_action("admin_enqueue_scripts", array($this, "addStudentPluginFiles"));

        add_action("wp_enqueue_scripts", array($this, "addSMSPluginFilesToFrontend"));

        add_filter("plugin_action_links_".SMS_PLUGIN_BASENAME, array($this, "plugin_settings_link"));

        // plugin_action_links_{plugin_folder_name/plugin_main_file}

        add_action("admin_init", array($this, "sms_plugin_settings"));

        // Admin login
        add_action("wp_ajax_sms_ajax_handler", array($this, "sms_ajax_handler"));

        // When admin dont login
        add_action("wp_ajax_nopriv_sms_ajax_handler", array($this, "sms_ajax_handler"));

        add_shortcode("my-tag", array($this, "sms_handle_first_shortcode"));

        add_shortcode("format-string", array($this, "sms_handle_string_format"));

        add_shortcode("sms-list-data", array($this, "smsHandleListData"));
     }
     
     // Add Settings link
     public function plugin_settings_link($links){

        $settings_link = '<a href="options-general.php?page=sms-plugin-settings">Settings</a>';
        array_unshift($links, $settings_link);
        
        return $links;
     }

     /* Ajax Request Handler */
     public function sms_ajax_handler(){

      global $wpdb;
      $table_prefix = $wpdb->prefix; // wp_

       if(isset($_REQUEST['param'])){

          if($_REQUEST['param'] == "save_form"){
 
            if(isset($_POST['wp_nonce_add_student']) && wp_verify_nonce($_POST['wp_nonce_add_student'], "wp_nonce_add_student")){

               // Nonce verified

               $name = sanitize_text_field($_POST['name']);
               $email = sanitize_text_field($_POST['email']);
               $gender = sanitize_text_field($_POST['gender']);
               $phone = sanitize_text_field($_POST['phone']);
               $profile_url = sanitize_text_field($_POST['profile_url']);
               $operation_type = sanitize_text_field($_POST['operation_type']);
               $profile_bio = $_POST['bio'];

               if($operation_type == "edit"){

                  // Edit operation
                  $student_id = $_REQUEST['student_id'];

                  $wpdb->update("{$table_prefix}student_system", array(
                     "name" => $name,
                     "email" => $email,
                     "phone_no" => $phone,
                     "gender" => $gender,
                     "profile_image" => $profile_url,
                     "profile_bio" => $profile_bio
                  ), array(
                     "id" => $student_id
                  ));

                  echo json_encode(array(
                     "status" => 1,
                     "message" => "Student updated successfully",
                     "data" => []
                  ));

               }elseif($operation_type == "add"){

                  // Add Operation
                  // Create student
                  $wpdb->insert("{$table_prefix}student_system", array(
                     "name" => $name,
                     "email" => $email,
                     "gender" => $gender,
                     "profile_image" => $profile_url,
                     "phone_no" => $phone,
                     "profile_bio" => $profile_bio
                  ));

                  $student_id = $wpdb->insert_id;

                  if($student_id > 0){

                     // Data created
                     echo json_encode(array(
                        "status" => 1,
                        "message" => "Student saved successfully",
                        "data" => []
                     ));
                  }else{

                     // Failed to create data
                     echo json_encode(array(
                        "status" => 0,
                        "message" => "Failed to add student",
                        "data" => []
                     ));
                  }
               }
            }
          }elseif($_REQUEST['param'] == "load_students"){

            $students = $wpdb->get_results("SELECT * FROM {$table_prefix}student_system", ARRAY_A);

            if(count($students) > 0){

               echo json_encode([
                  "status" => 1,
                  "message" => "Students data",
                  "data" => $students
               ]);
            }else{

               echo json_encode([
                  "status" => 0,
                  "message" => "No student found",
                  "data" => []
               ]);
            }
          }elseif($_REQUEST['param'] == "delete_student"){

             $student_id = $_REQUEST['student_id'];

             // Delete action
             $wpdb->delete("{$table_prefix}student_system", array(
               "id" => $student_id
             ));

             echo json_encode(array(
               "status" => 1,
               "message" => "Student deleted successfully"
             ));
          }elseif($_REQUEST['param'] == "frontend_form"){

            $name = sanitize_text_field($_POST['shortcode_name']);
            $email = sanitize_text_field($_POST['shortcode_email']);
            $gender = sanitize_text_field($_POST['shortcode_gender']);
            $mobile = sanitize_text_field($_POST['shortcode_mobile']);

            $wpdb->insert("{$table_prefix}student_system", array(
               "name" => $name,
               "email" => $email,
               "gender" => $gender,
               "phone_no" => $mobile
            ));

            if($wpdb->insert_id){

               echo json_encode(array(
                  "status" => 1,
                  "message" => "Data created"
               ));
            }else{

               echo json_encode(array(
                  "status" => 0,
                  "message" => "Failed to create data"
               ));
            }
          }
       }

       wp_die();
     }
     /* Ends... */
     
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
            `profile_bio` TEXT,
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
           ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
        ';

        include_once ABSPATH. 'wp-admin/includes/upgrade.php';

        dbDelta($sql);

        // Insert Test data
        $insertCommand = "INSERT INTO {$wpdb->prefix}student_system (name, email, phone_no, gender) VALUES ( 'Fake User 1', 'fakeuser1@gmail.com', '7418529635', 'male' ), ( 'Fake User 2', 'fakeuser2@gmail.com', '9632587415', 'female' ), ( 'Fake User 3', 'fakeuser3@gmail.com', '8521478963', 'male' )";

        $wpdb->query($insertCommand);
        
        // Create a wordpress Page
        $post_data = [
          "post_title" => "SMS List Page",
          "post_status" => "publish",
          "post_name" => "sms-list-page",
          "post_content" => "[sms-list-data]",
          "post_type" => "page"
        ];

        wp_insert_post($post_data);
     }

     // Drop Student Table
     public function dropStudentTable(){

        global $wpdb;

        $prefix = $wpdb->prefix; // wp_

        // Table Data Backup
        $db_user = DB_USER;
        $db_password = DB_PASSWORD;
        $db_name = DB_NAME;
        $wp_content_path = WP_CONTENT_DIR; 

        $version = time();
        $filename = "sms-db-tables-".$version.".sql";

        $backup_path = $wp_content_path."/sms-plugin/".$filename;

        // Tables
        $tables = ["{$wpdb->prefix}student_system"];

        $tables_names = implode(" ", $tables);

        if(!is_dir($wp_content_path."/sms-plugin")){

            mkdir($wp_content_path."/sms-plugin", 0777);
        }

        // Shell Execute command
        shell_exec("mysqldump -u {$db_user} -p{$db_password} {$db_name} $tables_names > {$backup_path}");

        // mysqldump -u root -pAdmin@123 db_name table_name1 table_name2 


        $sql = "DROP TABLE IF EXISTS ".$prefix."student_system";

        $wpdb->query($sql);
     }

     // Add Plugin File
     public function addStudentPluginFiles(){

        // Style
        wp_enqueue_style("datatable-css", SMS_PLUGIN_URL . "assets/css/jquery.dataTables.min.css", array(), "1.0", "all");
        // Message Plugin
        wp_enqueue_style("toastr-css", SMS_PLUGIN_URL . "assets/css/toastr.min.css", array(), "1.0", "all");
        wp_enqueue_style("custom-css", SMS_PLUGIN_URL . "assets/css/custom.css", array(), "1.0", "all");

        // Script
        wp_enqueue_media();
        wp_enqueue_script("datatable-js", SMS_PLUGIN_URL . "assets/js/jquery.dataTables.min.js", array("jquery"), "1.0");
        // Message Plugin
        wp_enqueue_script("toastr-js", SMS_PLUGIN_URL . "assets/js/toastr.min.js", array("jquery"), "1.0");
        wp_enqueue_script("script-js", SMS_PLUGIN_URL . "assets/js/script.js", array("jquery"), "1.0");

        $data = "var sms_ajax_url = '".admin_url('admin-ajax.php')."'";

        wp_add_inline_script("script-js", $data );
     }


     // callback function
     public function sms_handle_first_shortcode(){

        include_once SMS_PLUGIN_PATH . 'pages/form-shortcode.php';
     }

   public function sms_handle_string_format($attribute){

         $attributes = shortcode_atts(array(
            "color" => "black",
            "font-size" => "16px"
         ), $attribute);
   
         //print_r($attributes);
   
         return "<span style='color:{$attributes['color']}; font-size: {$attributes['font-size']}'>Welcome To Plugin Development Course</span>";
   }

   public function smsHandleFrontendForm(){

      //include_once SMS_PLUGIN_PATH. 'pages/form-shortcode.php';

      // PHP buffer
      ob_start(); // buffer starts
      include_once SMS_PLUGIN_PATH. 'pages/form-shortcode.php';
      $content = ob_get_contents(); // read buffer content
      ob_end_clean(); // buffer clean

      return $content;
   }

   public function addSMSPluginFilesToFrontend(){

      wp_enqueue_style("frontend-css", SMS_PLUGIN_URL . "assets/css/frontend.css", array(), "1.0", "all");

      wp_enqueue_script("frontend-js", SMS_PLUGIN_URL . "assets/js/frontend.js", array("jquery"), "1.0");

      $data = "var sms_ajax_url = '".admin_url('admin-ajax.php')."'";

      wp_add_inline_script("frontend-js", $data );
   }

   // SMS List Page function
   public function smsHandleListData(){

      global $wpdb;

      $students = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}student_system", ARRAY_A);

      ob_start();
      include_once SMS_PLUGIN_PATH. 'pages/sms-list-data.php';
      $content = ob_get_contents();
      ob_end_clean();

      return $content;
   }

}