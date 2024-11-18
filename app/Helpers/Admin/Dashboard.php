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
            'plugin_domain' => 'plugin-frame',
            'title' => __('Dashboard', 'plugin-frame'),
            'content' => __('Plugin Frame Admin Dashboard!', 'plugin-frame'),
            'description' => 'Plugin Frame Dashboard description for without text-domain',
        ]);
    }
}
