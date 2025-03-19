<?php

namespace PluginFrame\Core\Hooks;

// Exit if accessed directly
if (!defined('ABSPATH')) { exit; }

use Exception;

class Deactivation
{
    public function __construct()
    {
        register_deactivation_hook(PLUGIN_FRAME_FILE, [$this, 'handleDeactivation']);
    }

    public function handleDeactivation(): void
    {
        do_action('plugin_frame_pre_deactivation');
        
        try {
            $this->deactivate();
            do_action('plugin_frame_post_deactivation');
            error_log(PLUGIN_FRAME_NAME . ' Deactivated successfully.');
            pf_logs(PLUGIN_FRAME_NAME . ' Deactivated successfully.');
        } catch (Exception $e) {
            error_log(PLUGIN_FRAME_NAME . ' Deactivation failed: ' . $e->getMessage());
            wp_die(__('Plugin deactivation failed. Check error logs.', 'plugin-frame'));
        }
    }

    protected function deactivate(): void
    {
        // Extension point for core + custom logic
        do_action('plugin_frame_on_deactivation');
    }
}