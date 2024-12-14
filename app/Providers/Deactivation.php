<?php

namespace PluginFrame\Providers;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Deactivation
{
    protected $deactivationService;

    public function runDeactivation()
    {
        // Initialize the service
        $this->deactivationService = new \PluginFrame\Services\Deactivation();

        // Register the deactivation
        $this->deactivationService->registerDeactivationHook([$this, 'deactivate']);

        // Add a pf_log that plugin deactivated successfully
        pf_log(PLUGIN_FRAME_NAME . ' deactivated successfully.');
    }

    /**
     * Deactivation logic executed during plugin deactivation.
     */
    public function deactivate()
    {
        // Remove scheduled events (cron jobs)
        $this->removeScheduleCronJobs();

       // Perform other necessary deactivation tasks when plugin is deactivated
        
    }

    /**
     * Remove scheduled cron jobs of plugin frame
     */
    protected function removeScheduleCronJobs()
    {
        if (wp_next_scheduled('pluginframe_heartbeat_event')) {
            wp_clear_scheduled_hook('pluginframe_heartbeat_event');
        }
    }

}
