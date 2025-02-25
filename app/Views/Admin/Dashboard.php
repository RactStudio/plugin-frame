<?php

namespace PluginFrame\Views\Admin;

use PluginFrame\Services\Views;

// Exit if accessed directly
defined('ABSPATH') || exit;

class Dashboard
{
    /**
     * Render the content of the page.
     * @return void
     */
    public function render(): void
    {
        echo Views::render('admin/dashboard', 'twig', [
            'plugin_domain'    => PLUGIN_FRAME_SLUG,
            'plugin_frame_name'=> PLUGIN_FRAME_NAME,
            'plugin_frame_url' => PLUGIN_FRAME_URL,
            'title'            => __('Dashboard', 'plugin-frame'),
            'content'          => __('Plugin Frame Admin Dashboard!', 'plugin-frame'),
            'description'      => __('Plugin Frame Dashboard description for without text-domain', 'plugin-frame'),
        ]);
    }
}
