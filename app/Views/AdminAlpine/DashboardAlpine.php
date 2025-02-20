<?php

namespace PluginFrame\Helpers\AdminAlpine;

use PluginFrame\Services\Views;

// Exit if accessed directly
defined('ABSPATH') || exit;

class DashboardAlpine
{
    /**
     * Render the content of the page.
     * @return void
     */
    public function render(): void
    {
        echo Views::render('admin-alpine/dashboard', 'twig', [
            'plugin_domain'    => 'plugin-frame',
            'plugin_frame_name'=> PLUGIN_FRAME_NAME,
            'title'            => __('Dashboard', 'plugin-frame'),
            'content'          => __('Plugin Frame Admin Dashboard!', 'plugin-frame'),
            'description'      => __('Plugin Frame Dashboard description for without text-domain', 'plugin-frame'),
            'plugin_frame_url' => PLUGIN_FRAME_URL,
        ]);
    }
}
