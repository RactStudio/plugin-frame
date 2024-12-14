<?php

namespace PluginFrame\Hooks;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Activation
{
    /**
     * Register the activation hook.
     */
    public function __construct()
    {
        register_activation_hook(PLUGIN_FRAME_FILE, [$this, 'activate']);
    }

    /**
     * Activation logic executed during plugin activation.
     */
    public function activate(): void
    {
        // Run scheduled events (cron jobs)
        self::runScheduleCronJobs();

        // Add a log entry indicating successful activation
        if (function_exists('pf_log')) {
            pf_log(PLUGIN_FRAME_NAME . ' activated successfully.');
        }
    }

    /**
     * Run scheduled cron jobs of plugin frame.
     */
    protected static function runScheduleCronJobs(): void
    {
        if (!wp_next_scheduled('pluginframe_heartbeat_event')) {
            wp_schedule_event(time(), '5 minutes', 'pluginframe_heartbeat_event');
        }
    }
}
