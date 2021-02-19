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
// define( 'MY_ACF_PATH', plugin_dir_path( __FILE__ ) . '/includes/advanced-custom-fields-pro/' );
// define( 'MY_ACF_URL', plugin_dir_path( __FILE__ ) . '/includes/advanced-custom-fields-pro/' );

// Include the ACF plugin.
// include_once( MY_ACF_PATH . 'acf.php' );

// Customize the url setting to fix incorrect asset URLs.
// add_filter('acf/settings/url', 'my_acf_settings_url');
// function my_acf_settings_url( $url ) {
//     return MY_ACF_URL;
// }


include( plugin_dir_path( __FILE__ ) . 'register-post-type.php');
include( plugin_dir_path( __FILE__ ) . 'form-handling.php');
include( plugin_dir_path( __FILE__ ) . 'results.php');
// include( plugin_dir_path( __FILE__ ) . 'create-form.php');
// include( plugin_dir_path( __FILE__ ) . 'admin.php');




