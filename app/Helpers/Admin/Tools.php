<?php

namespace PluginFrame\Helpers\Admin;

use PluginFrame\Services\Views;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Tools
{
    /**
     * Render the content of the dashboard page.
     *
     * @return void
     */
    public function render(): void
    {
        echo Views::render('admin/tools', [
            'plugin_domain' => 'plugin-frame',
            'title' => __('Dashboard', 'plugin-frame'),
            'content' => __('Plugin Frame Tools Box!', 'plugin-frame'),
            'description' => 'Plugin Frame Tools box description for without text-domain',
        ]);
    }
}
