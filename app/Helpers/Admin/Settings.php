<?php

namespace PluginFrame\Helpers\Admin;

use PluginFrame\Services\Views;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Settings
{
    /**
     * Render the settings page content.
     *
     * @return void
     */
    public function render(): void
    {
        echo Views::render('admin/settings', [
            'plugin_domain' => PLUGIN_FRAME_DOMAIN,
            'title' => __('Settings', PLUGIN_FRAME_DOMAIN),
            'content' => __('Plugin Frame Settings', PLUGIN_FRAME_DOMAIN),
            'description' => 'Plugin Frame description for without text-domain',
        ]);
    }
}
