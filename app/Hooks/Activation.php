<?php

namespace PluginFrame\Hooks;

use Exception;

// Exit if accessed directly
if (!defined('ABSPATH')) { exit; }

class Activation
{
    /**
     * Register the activation hook.
     */
    public function __construct()
    {
        register_activation_hook(PLUGIN_FRAME_FILE, [$this, 'handleActivation']);
    }

    /**
     * Handle the activation process.
     */
    public function handleActivation(): void
    {
        // Execute pre-activation hooks
        do_action('plugin_frame_pre_activation');

        try {
            // Core activation logic
            $this->activate();

            // Execute post-activation hooks
            do_action('plugin_frame_post_activation');

            // Log successful activation
            error_log(PLUGIN_FRAME_NAME . ' Activated successfully.');

        } catch (Exception $e) {
            // Log the error
            error_log(PLUGIN_FRAME_NAME . ' Activation failed: ' . $e->getMessage());

            // Stop execution and show an error message
            wp_die(__('Plugin activation failed. Please check the error logs for details.', 'plugin-frame'));
        }
    }

    /**
     * Activation logic executed during plugin activation.
     */
    private function activate(): void
    {
        // Example: Perform database setup or initial settings
        // $this->setupDatabase();
        // $this->setupInitialOptions();

        // Extension point for additional tasks
        do_action('plugin_frame_on_activation');
    }

    /**
     * Perform database setup during activation.
     */
    private function setupDatabase(): void
    {
        global $wpdb;

        // Example: Create a custom database table
        $tableName = $wpdb->prefix . 'example_table';
        $charsetCollate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $tableName (
            id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) $charsetCollate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);

        error_log('Database setup completed.');
    }

    /**
     * Setup initial plugin options.
     */
    private function setupInitialOptions(): void
    {
        // Example: Add default options to the WordPress options table
        if (!get_option('plugin_frame_default_option')) {
            add_option('plugin_frame_default_option', 'default_value');
            error_log('Initial plugin options set up.');
        }
    }
}
