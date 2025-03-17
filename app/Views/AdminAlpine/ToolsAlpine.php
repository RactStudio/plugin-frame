<?php

namespace PluginFrame\Views\AdminAlpine;

use PluginFrame\Core\Services\Views;

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
            'plugin_domain'    => PLUGIN_FRAME_SLUG,
            'plugin_frame_name'=> PLUGIN_FRAME_NAME,
            'plugin_frame_url' => PLUGIN_FRAME_URL,
            'title'            => __('Tools', 'plugin-frame'),
            'content'          => __('Plugin Frame Tools Dashboard!', 'plugin-frame'),
            'description'      => __('Plugin Frame Tools description for without text-domain', 'plugin-frame'),
        ]);
    }
}
