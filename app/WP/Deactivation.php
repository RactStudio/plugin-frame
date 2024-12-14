<?php

namespace PluginFrame\Services;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Deactivation
{
    /**
     * Register the deactivation hook for the provided callable.
     *
     * @param callable $callback
     */
    public function registerDeactivationHook($callback): void
    {
        register_deactivation_hook(PLUGIN_FRAME_FILE, $callback);
    }
}
