<?php

namespace PluginFrame\Views\Admin;

use PluginFrame\Core\Services\Views;
use PluginFrame\Core\Services\Options\OptionManager;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Dashboard
{
    /**
     * @var OptionManager
     */
    private OptionManager $options;

    /**
     * Render the content of the page.
     * @return void
     */
    public function render(): void
    {
        // $current_footer = $this->options->get('pf_footer_text', 'default fallback');

        echo Views::render('admin/dashboard', 'twig', [
            'plugin_domain'    => PLUGIN_FRAME_SLUG,
            'plugin_frame_name'=> PLUGIN_FRAME_NAME,
            // 'pf_footer_text'   => $current_footer,
            'plugin_frame_url' => PLUGIN_FRAME_URL,
            'title'            => __('Dashboard', 'plugin-frame'),
            'content'          => __('Plugin Frame Admin Dashboard!', 'plugin-frame'),
            'description'      => __('Plugin Frame Dashboard description for without text-domain', 'plugin-frame'),
        ]);
    }
}
