<?php
/**
 * Plugin Name: Plugin Frame
 * Plugin URI:  https://wp-plugin-frame.com
 * Description: A modern WordPress plugin development framework.
 * Version:     1.0.0
 * Author:      Mahamudul Hasan Rubel
 * Author URI:  https://mhrubel.com
 * Text Domain: plugin-frame
 * Domain Path: /languages
 * License:     GPL2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define constants for plugin directory paths
define( 'PLUGIN_FRAME_DIR', plugin_dir_path( __FILE__ ) );
define( 'PLUGIN_FRAME_URL', plugin_dir_url( __FILE__ ) );

/**
 * Plugin initialization
 */
function plugin_frame_init() {
    // Fires when the plugin starts loading
    do_action( 'plugin_frame_load_start' );

    // Autoload Composer dependencies
    if ( file_exists( PLUGIN_FRAME_DIR . 'vendor/autoload.php' ) ) {
        require_once PLUGIN_FRAME_DIR . 'vendor/autoload.php';
    }

    // Include the main configuration file to load framework files
    require_once PLUGIN_FRAME_DIR . 'config/main.php';

    // Fires when the plugin finishes loading
    do_action( 'plugin_frame_load_end' );
}
add_action( 'plugins_loaded', 'plugin_frame_init', 1 ); // Prioritize loading if needed
