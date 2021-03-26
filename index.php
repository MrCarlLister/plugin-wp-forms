<?php
/**
 * Plugin Name:  My form validation functions
 * Plugin URI:   https://www.mrcarllister.co.uk/
 * Description:  Used for submitting forms and dealing with validation of fields, creating tables and updating records
 * Version:      1.0.0
 * Author:       Carl Lister
 * Author URI:   https://www.mrcarllister.co.uk/
 */

// Define path and URL to the ACF plugin.
define( 'MY_ACF_PATH', plugin_dir_path( __FILE__ ) . '/includes/acf/' );
define( 'MY_ACF_URL', plugin_dir_path( __FILE__ ) . '/includes/acf/' );

// Include the ACF plugin.
include_once( MY_ACF_PATH . 'acf.php' );

include( plugin_dir_path( __FILE__ ) . 'register-forms.php');
include( plugin_dir_path( __FILE__ ) . 'email-handling.php');
include( plugin_dir_path( __FILE__ ) . 'form-handling.php');
include( plugin_dir_path( __FILE__ ) . 'submission-handling.php');
include( plugin_dir_path( __FILE__ ) . 'results.php');
include( plugin_dir_path( __FILE__ ) . 'gutenberg.php');


// include( plugin_dir_path( __FILE__ ) . 'create-form.php');
// include( plugin_dir_path( __FILE__ ) . 'admin.php');




