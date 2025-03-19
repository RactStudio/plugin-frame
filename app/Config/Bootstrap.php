<?php

namespace PluginFrame\Config;

use PluginFrame\Core\Helpers\BootstrapHelper;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Define plugin constants if not already defined
 */
if ( ! defined( 'PLUGIN_FRAME_NAME' ) ) {
    define( 'PLUGIN_FRAME_NAME', 'Plugin Frame' ); // Required
}
if ( ! defined( 'PLUGIN_FRAME_VERSION' ) ) {
    define( 'PLUGIN_FRAME_VERSION', '0.9.1' ); // Required
}
if ( ! defined( 'PLUGIN_FRAME_MIN_WP' ) ) {
    define( 'PLUGIN_FRAME_MIN_WP', '5.2' ); // Required
}
if ( ! defined( 'PLUGIN_FRAME_MAX_WP' ) ) {
    define( 'PLUGIN_FRAME_MAX_WP', '6.7.2' ); // Required
}
if ( ! defined( 'PLUGIN_FRAME_MIN_PHP' ) ) {
    define( 'PLUGIN_FRAME_MIN_PHP', '7.4' ); // Required
}
if ( ! defined( 'PLUGIN_FRAME_SLUG' ) ) {
    define( 'PLUGIN_FRAME_SLUG', 'plugin-frame' ); // Required
}
if ( ! defined( 'PLUGIN_FRAME_FILE' ) ) {
    define( 'PLUGIN_FRAME_FILE', dirname( __DIR__, 2 ) . '/' . PLUGIN_FRAME_SLUG . '.php' ); // Required
}
if ( ! defined( 'PLUGIN_FRAME_DIR' ) ) {
    define( 'PLUGIN_FRAME_DIR', plugin_dir_path( PLUGIN_FRAME_FILE ) ); // Required
}
if ( ! defined( 'PLUGIN_FRAME_URL' ) ) {
    define( 'PLUGIN_FRAME_URL', plugin_dir_url( PLUGIN_FRAME_FILE ) ); // Required
}
if ( ! defined( 'PLUGIN_FRAME_BASENAME' ) ) {
    define( 'PLUGIN_FRAME_BASENAME', plugin_basename( PLUGIN_FRAME_FILE ) ); // Required
}

class Bootstrap
{
    protected $bootHelper;

    public function __construct()
    {
        //Load Utilities globally
        $debug_log_file = PLUGIN_FRAME_DIR . 'app/Core/Utilities/UtilsLoader.php';
        if ( file_exists( $debug_log_file ) ) {
            require_once $debug_log_file;
        }
        
        // Load Bootstrap Helper
        require_once PLUGIN_FRAME_DIR . 'app/Core/Helpers/BootstrapHelper.php';
        $this->bootHelper = new BootstrapHelper();

        // Perform PHP version check early before plugin execution.
        if ( ! $this->bootHelper->is_php_version_compatible() )
        {
            return; // Stop plugin execution if PHP version requirement is not met.
        }

        // Initialize plugin functionalities after all plugins are loaded.
        add_action('plugins_loaded', [$this->bootHelper, 'initialize_plugin']);

        // Register Hooks - activation, deactivation, uninstallation, upgrade, and updater.
        $this->bootHelper->on_plugin_activation();
        $this->bootHelper->on_plugin_deactivation();
        $this->bootHelper->on_plugin_uninstall();
        $this->bootHelper->on_plugin_upgrade();
        $this->bootHelper->on_plugin_updater();
    }

}