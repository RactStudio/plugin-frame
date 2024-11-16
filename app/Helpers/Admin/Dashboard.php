<?php

namespace PluginFrame\Helpers\Admin;

use PluginFrame\Services\Views;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Dashboard
{
    /**
     * Render the content of the dashboard page.
     *
     * @return void
     */
    public function render(): void
    {
        echo Views::render('admin/dashboard', [
            'plugin_domain' => PLUGIN_FRAME_DOMAIN,
            'title' => __('Dashboard', PLUGIN_FRAME_DOMAIN),
            'content' => __('Plugin Frame Admin Dashboard!', PLUGIN_FRAME_DOMAIN),
            'description' => 'Plugin Frame description for without text-domain',
        ]);
    }
}
