<?php

namespace PluginFrame\Views\AdminAlpine;

use PluginFrame\Services\Views;

// Exit if accessed directly
defined('ABSPATH') || exit;

class ToolsAlpine
{
    /**
     * Render the content of the page.
     * @return void
     */
    public function render(): void
    {
        echo Views::render('admin-alpine/tools',  'twig', [
            'plugin_domain'    => 'plugin-frame',
            'plugin_frame_name'=> PLUGIN_FRAME_NAME,
            'title'            => __('Tools', 'plugin-frame'),
            'content'          => __('Plugin Frame Tools Dashboard!', 'plugin-frame'),
            'description'      => __('Plugin Frame Tools description for without text-domain', 'plugin-frame'),
            'plugin_frame_url' => PLUGIN_FRAME_URL,
        ]);
    }
}
