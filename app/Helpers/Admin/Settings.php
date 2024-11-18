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
            'plugin_domain' => 'plugin-frame',
            'title' => __('Settings', 'plugin-frame'),
            'content' => __('Plugin Frame Settings', 'plugin-frame'),
            'description' => 'Plugin Frame Settings description for without text-domain',
        ]);
    }
}
