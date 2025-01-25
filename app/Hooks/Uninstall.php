<?php

namespace PluginFrame\Hooks;

use Exception;

// Exit if accessed directly
if (!defined('ABSPATH')) { exit; }

class Uninstall
{
    /**
     * Register the uninstall logic.
     */
    public static function init(): void
    {
        register_uninstall_hook(PLUGIN_FRAME_FILE, [__CLASS__, 'handleUninstall']);
    }

    /**
     * Handle the uninstallation process.
     */
    public static function handleUninstall(): void
    {
        // Execute pre-uninstall hooks
        do_action('plugin_frame_pre_uninstall');

        try {
            // Core uninstall logic
            self::uninstallCore();

            // Execute post-uninstall hooks
            do_action('plugin_frame_post_uninstall');

            // Log successful uninstallation
            error_log(PLUGIN_FRAME_NAME . ' uninstalled successfully.');

        } catch (Exception $e) {
            // Log the error
            error_log(PLUGIN_FRAME_NAME . ' Uninstallation failed: ' . $e->getMessage());

            // Show an error message to the admin if necessary
            wp_die(__('Plugin uninstallation failed. Please check the error logs for details.', 'plugin-frame'));
        }
    }

    /**
     * Core uninstall logic executed during plugin uninstallation.
     */
    private static function uninstallCore(): void
    {
        // Example: Perform database cleanup or remove plugin-specific options
        // self::cleanupDatabase();
        // self::removePluginOptions();

        // Extension point for additional tasks
        do_action('plugin_frame_on_uninstall');
    }

    /**
     * Perform database cleanup during uninstallation.
     */
    private static function cleanupDatabase(): void
    {
        global $wpdb;

        // Example: Drop a custom database table
        $tableName = $wpdb->prefix . 'example_table';
        $wpdb->query("DROP TABLE IF EXISTS $tableName");

        error_log('Database cleanup completed.');
    }

    /**
     * Remove plugin-specific options and settings.
     */
    private static function removePluginOptions(): void
    {
        // Example: Delete plugin-specific options
        delete_option('plugin_frame_version');
        delete_option('plugin_frame_settings');

        error_log('Plugin-specific options removed.');
    }
}
