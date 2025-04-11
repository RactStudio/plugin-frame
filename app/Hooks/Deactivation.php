<?php

namespace PluginFrame\Hooks;

// Exit if accessed directly
if (!defined('ABSPATH')) { exit; }

require_once PLUGIN_FRAME_DIR . 'app/Core/Hooks/Deactivation.php';
use PluginFrame\Core\Hooks\Deactivation as CoreDeactivation;

class Deactivation extends CoreDeactivation
{
    protected function deactivate(): void
    {
        parent::deactivate(); // Preserve core hooks
        // $this->disablePluginFeatures();
    }

    protected function disablePluginFeatures(): void
    {
        // Custom cleanup tasks (EXAMPLE)
        wp_clear_scheduled_hook('plugin_frame_cron_event');
        update_option('plugin_frame_is_active', false);
    }
}