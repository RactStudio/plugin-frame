<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Load Log and debugging utilities.
 */
 if (!function_exists('plugin_frame_load_utils')) {
    function plugin_frame_load_utils(): void {
        $log_file = PLUGIN_FRAME_DIR . 'app/Core/Utilities/PFlogs/Helpers.php';
        if ( file_exists( $log_file ) ) {
            require_once $log_file;
            require_once PLUGIN_FRAME_DIR . 'app/Core/Utilities/PFlogs/LogCleaner.php';
        } else {
            error_log( 'PF Log helper not found. File: ' . $log_file );
        }
        
        $debug_file = PLUGIN_FRAME_DIR . 'app/Core/Utilities/Debug/Helpers.php';
        if ( file_exists( $debug_file ) ) {
            require_once $debug_file;
        } else {
            error_log( 'Debug helper not found. File: ' . $debug_file );
        }
    }
}

// Call utilities load before plugins_loaded fires.
plugin_frame_load_utils();
