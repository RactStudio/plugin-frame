<?php

namespace PluginFrame\Hooks;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Deactivation
{
    /**
     * Register the deactivation hook.
     */
    public function __construct()
    {
        register_deactivation_hook(PLUGIN_FRAME_FILE, [$this, 'deactivate']);
    }

    /**
     * Deactivation logic executed during plugin deactivation.
     */
    public function deactivate(): void
    {
        // Remove scheduled events (cron jobs)
        self::removeScheduleCronJobs();

        // Add a log entry indicating successful deactivation
        if (function_exists('pf_log')) {
            pf_log(PLUGIN_FRAME_NAME . ' deactivated successfully.');
        }
    }

    /**
     * Remove scheduled cron jobs of plugin frame.
     */
    protected static function removeScheduleCronJobs(): void
    {
        if (wp_next_scheduled('pluginframe_heartbeat_event')) {
            wp_clear_scheduled_hook('pluginframe_heartbeat_event');
        }
    }
}
