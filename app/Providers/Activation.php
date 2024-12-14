<?php

namespace PluginFrame\Providers;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Activation
{
    protected $activationService;

    public function runActivation(): void
    {
        // Initialize the service
        $this->activationService = new \PluginFrame\Services\Activation();

        // Register the activation
        $this->activationService->registerActivationHook([$this, 'activate']);
        
        // Add a pf_log that plugin activated successfully
        pf_log(PLUGIN_FRAME_NAME . ' activated successfully.');
    }

    /**
     * Activation logic executed during plugin activation.
     */
    public function activate(): void
    {
        // Run scheduled events (cron jobs)
        $this->runScheduleCronJobs();
        
       // Perform other necessary activation tasks when plugin is activated
        
    }

    /**
     * Run scheduled cron jobs of plugin frame
     */
    protected function runScheduleCronJobs(): void
    {
        if (!wp_next_scheduled('pluginframe_heartbeat_event')) {
            wp_schedule_event(time(), '5 minutes', 'pluginframe_heartbeat_event');
        }
    }

}