<?php

namespace PluginFrame\Hooks;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

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
        // Add a log entry indicating successful deactivation
        pf_log( PLUGIN_FRAME_NAME . ' Deactivated successfully.');

        // Add yours
    }
}
