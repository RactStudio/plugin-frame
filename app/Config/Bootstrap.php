<?php

namespace PluginFrame\Config;

use PluginFrame\Helpers\BootstrapHelper;

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
if ( ! defined( 'PLUGIN_FRAME_NAMESPACE' ) ) {
    define( 'PLUGIN_FRAME_NAMESPACE', 'PluginFrame' ); // Required
}
if ( ! defined( 'PLUGIN_FRAME_SLUG' ) ) {
    define( 'PLUGIN_FRAME_SLUG', 'plugin-frame' ); // Required
}
if ( ! defined( 'PLUGIN_FRAME_PREFIX' ) ) {
    define( 'PLUGIN_FRAME_PREFIX', 'plugin_frame' ); // Required
}
if ( ! defined( 'PLUGIN_FRAME_FILE' ) ) {
    define( 'PLUGIN_FRAME_FILE', dirname( __DIR__, 2 ) . '/' . PLUGIN_FRAME_SLUG . '.php' ); // Required [MUST BE HERE]
}
if ( ! defined( 'PLUGIN_FRAME_DIR' ) ) {
    define( 'PLUGIN_FRAME_DIR', plugin_dir_path( PLUGIN_FRAME_FILE ) ); // Required
}
if ( ! defined( 'PLUGIN_FRAME_URL' ) ) {
    define( 'PLUGIN_FRAME_URL', plugin_dir_url( PLUGIN_FRAME_FILE ) ); // Required
}
if ( ! defined( 'PLUGIN_FRAME_MIN_PHP' ) ) {
    define( 'PLUGIN_FRAME_MIN_PHP', '7.4' ); // Required
}
if ( ! defined( 'PLUGIN_FRAME_BASENAME' ) ) {
    define( 'PLUGIN_FRAME_BASENAME', plugin_basename( PLUGIN_FRAME_FILE ) ); // Required
}

class Bootstrap
{
    protected $bootHelper;

    public function __construct()
    {
        require_once PLUGIN_FRAME_DIR . 'app/Helpers/BootstrapHelper.php';
        $this->bootHelper = new BootstrapHelper();

        // Perform PHP version check early before plugin execution.
        if ( ! $this->bootHelper->is_php_version_compatible() )
        {
            return; // Stop plugin execution if PHP version requirement is not met.
        }

        // Initialize plugin functionalities after all plugins are loaded.
        add_action('plugins_loaded', [$this->bootHelper, 'initialize_plugin']);

        // Register WP hooks.
        // Plugin - activation, deactivation, uninstallation, and upgrade.
        $this->bootHelper->on_plugin_activation();
        $this->bootHelper->on_plugin_deactivation();
        $this->bootHelper->on_plugin_uninstall();
        $this->bootHelper->on_plugin_upgrade();
    }

}