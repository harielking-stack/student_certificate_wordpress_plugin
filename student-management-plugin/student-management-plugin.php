<?php
/**
 * Plugin Name: Student Management Plugin
 * Description: A plugin to manage student details and certificates.
 * Version: 1.1
 * Author: Alfrin
 */

if (!defined('ABSPATH')) {
   exit; // Exit if accessed directly
}

// Include Admin and Frontend Functions
include_once plugin_dir_path(__FILE__) . 'includes/admin-functions.php';
include_once plugin_dir_path(__FILE__) . 'includes/frontend-functions.php';

// Enqueue styles
function smp_enqueue_styles() {
   wp_enqueue_style('smp-styles', plugin_dir_url(__FILE__) . 'assets/style.css');
}
add_action('wp_enqueue_scripts', 'smp_enqueue_styles');
add_action('admin_enqueue_scripts', 'smp_enqueue_styles');

// Create custom database table upon plugin activation
function smp_create_student_table() {
   global $wpdb;
   $table_name = $wpdb->prefix . 'student';
   $charset_collate = $wpdb->get_charset_collate();

   // Load the dbDelta function
   require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

   $sql = "CREATE TABLE $table_name (
       id mediumint(9) NOT NULL AUTO_INCREMENT,
       student_id VARCHAR(50) NOT NULL,
       student_name VARCHAR(100) NOT NULL,
       course VARCHAR(100) NOT NULL,
       phone VARCHAR(15),
       email VARCHAR(100),
       photo VARCHAR(255),
       certificate VARCHAR(255),
       PRIMARY KEY (id)
   ) $charset_collate;";

   dbDelta($sql); // Run dbDelta to create the table
}

register_activation_hook(__FILE__, 'smp_create_student_table');

?>