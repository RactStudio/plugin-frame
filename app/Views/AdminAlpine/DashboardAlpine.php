<?php

namespace PluginFrame\Views\AdminAlpine;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

use PluginFrame\Core\Services\Views;

class DashboardAlpine
{
    /**
     * Render the content of the page.
     * @return void
     */
    public function render(): void
    {
        echo Views::render('admin-alpine/dashboard', 'twig', [
            'plugin_domain'    => PLUGIN_FRAME_SLUG,
            'plugin_frame_name'=> PLUGIN_FRAME_NAME,
            'plugin_frame_url' => PLUGIN_FRAME_URL,
            'title'            => __('Dashboard', 'plugin-frame'),
            'content'          => __('Plugin Frame Admin Dashboard!', 'plugin-frame'),
            'description'      => __('Plugin Frame Dashboard description for without text-domain', 'plugin-frame'),
        ]);
    }
}
