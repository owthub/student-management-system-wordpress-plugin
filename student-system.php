<?php 

/*
* Plugin name: Student Management System
* Description: This is a test plugin to manage student data
* Plugin URI: https://www.example.com/student-management-system
* Author: Online Web Tutor
* Author URI: https://www.example.com
* Version: 1.0
* Requires PHP: 7.4
* Requires at least: 6.3.2
*/

define("SMS_PLUGIN_PATH", plugin_dir_path(__FILE__));
define("SMS_PLUGIN_URL", plugin_dir_url(__FILE__));
define("SMS_PLUGIN_BASENAME", plugin_basename(__FILE__));

include_once SMS_PLUGIN_PATH.'class/StudentManagement.php';

$studentManageObject = new StudentManagement();

register_activation_hook(__FILE__, array($studentManageObject, "createStudentTable"));

register_deactivation_hook(__FILE__, array($studentManageObject, "dropStudentTable"));
