<?php

namespace PluginFrame\Helpers\Admin;

use PluginFrame\Services\Views;

// Exit if accessed directly
defined('ABSPATH') || exit;

class Settings
{
    /**
     * Render the content of the page.
     * @return void
     */
    public function render(): void
    {
        echo Views::render('admin/settings',  'twig', [
            'plugin_domain'    => 'plugin-frame',
            'plugin_frame_name'=> PLUGIN_FRAME_NAME,
            'title'            => __('Settings', 'plugin-frame'),
            'content'          => __('Plugin Frame Settings Dashboard!', 'plugin-frame'),
            'description'      => __('Plugin Frame Settings description for without text-domain', 'plugin-frame'),
            'plugin_frame_url' => PLUGIN_FRAME_URL,
        ]);
    }
}
