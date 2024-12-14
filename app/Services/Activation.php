<?php

namespace PluginFrame\Services;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Activation
{
    /**
     * Register the activation hook for the provided callable.
     *
     * @param callable $callback
     */
    public function registerActivationHook($callback): void
    {
        register_activation_hook(PLUGIN_FRAME_FILE, $callback);
    }
}
