<?php

namespace PluginFrame\Hooks;

use Exception;

// Exit if accessed directly
if (!defined('ABSPATH')) { exit; }

class Deactivation
{
    /**
     * Register the deactivation hook.
     */
    public function __construct()
    {
        register_deactivation_hook(PLUGIN_FRAME_FILE, [$this, 'handleDeactivation']);
    }

    /**
     * Handle the deactivation process.
     */
    public function handleDeactivation(): void
    {
        // Execute pre-deactivation hooks
        do_action('plugin_frame_pre_deactivation');

        try {
            // Core deactivation logic
            $this->deactivate();

            // Execute post-deactivation hooks
            do_action('plugin_frame_post_deactivation');

            // Log successful deactivation
            error_log(PLUGIN_FRAME_NAME . ' deactivated successfully.');

        } catch (Exception $e) {
            // Log the error
            error_log(PLUGIN_FRAME_NAME . ' Deactivation failed: ' . $e->getMessage());

            // Show an error message to the admin if necessary
            wp_die(__('Plugin deactivation failed. Please check the error logs for details.', 'plugin-frame'));
        }
    }

    /**
     * Core deactivation logic executed during plugin deactivation.
     */
    private function deactivate(): void
    {
        // Example: Perform temporary cleanup or disable plugin functionality
        // $this->disablePluginFeatures();

        // Developer-friendly extension point for additional tasks
        do_action('plugin_frame_on_deactivation');
    }

    /**
     * Disable plugin-specific features or cleanup tasks.
     */
    private function disablePluginFeatures(): void
    {
        // Example: Disable scheduled cron jobs
        wp_clear_scheduled_hook('plugin_frame_cron_event');

        // Example: Set a flag indicating the plugin is deactivated
        update_option('plugin_frame_is_active', false);

        error_log('Plugin-specific features disabled.');
    }
}
