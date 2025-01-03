<?php

namespace PluginFrame\Hooks;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Uninstall
{
    /**
     * Register the uninstall logic.
     */
    public static function init(): void
    {
        register_uninstall_hook(PLUGIN_FRAME_FILE, [__CLASS__, 'onUninstall']);
    }

    /**
     * Logic executed during plugin uninstallation.
     */
    public static function onUninstall(): void
    {
        // Log uninstall process
        error_log(PLUGIN_FRAME_NAME . ' uninstall process started.');

        // Perform cleanup tasks
        self::performCleanup();

        // Log success
        error_log(PLUGIN_FRAME_NAME . ' uninstalled successfully.');
    }

    /**
     * Perform cleanup tasks.
     */
    private static function performCleanup(): void
    {
        global $wpdb;

        // Example: Delete plugin-specific options
        delete_option('plugin_frame_version');
        delete_option('plugin_frame_settings');

        // Example: Drop custom database tables
        // $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}example_table");

        // Example: Remove custom user meta
        // delete_metadata('user', 0, 'plugin_frame_meta_key', '', true);
    }
}
