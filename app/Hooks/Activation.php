<?php

namespace PluginFrame\Hooks;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

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
        // Add a log entry indicating successful activation
        pf_log(PLUGIN_FRAME_NAME . ' Activated successfully.');

        // Add yours
    }

}
