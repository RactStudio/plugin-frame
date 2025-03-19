<?php

namespace PluginFrame\Core\Hooks;

// Exit if accessed directly
if (!defined('ABSPATH')) { exit; }

use Exception;

class Activation
{
    public function __construct()
    {
        register_activation_hook(PLUGIN_FRAME_FILE, [$this, 'handleActivation']);
    }

    public function handleActivation(): void
    {
        do_action('plugin_frame_pre_activation');
        
        try {
            $this->activate();
            do_action('plugin_frame_post_activation');
            error_log(PLUGIN_FRAME_NAME . ' Activated successfully.');
            pf_logs(PLUGIN_FRAME_NAME . ' Activated successfully.');
        } catch (Exception $e) {
            error_log(PLUGIN_FRAME_NAME . ' Activation failed: ' . $e->getMessage());
            wp_die(__('Plugin activation failed. Check error logs.', 'plugin-frame'));
        }
    }

    protected function activate(): void
    {
        // Extension point for core + custom logic
        do_action('plugin_frame_on_activation');
    }
}