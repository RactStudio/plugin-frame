<?php

namespace PluginFrame\Helpers\Admin;

use PluginFrame\Services\Views;

// Exit if accessed directly
defined('ABSPATH') || exit;

class Tools
{
    /**
     * Captured WordPress notices.
     * @var string|null
     */
    protected ?string $wp_notices = null;

    public function __construct()
    {
        // Capture notices
        $this->wp_notices = $this->get_wp_notices();

        // Remove admin notices to prevent auto-injection
        $this->disable_auto_injection();
    }

    /**
     * Render the content of the page.
     * @return void
     */
    public function render(): void
    {
        echo Views::render('admin/tools', [
            'plugin_domain'    => 'plugin-frame',
            'title'            => __('Tools', 'plugin-frame'),
            'content'          => __('Plugin Frame Tools Dashboard!', 'plugin-frame'),
            'description'      => __('Plugin Frame Tools description for without text-domain', 'plugin-frame'),
            'plugin_frame_url' => PLUGIN_FRAME_URL,
            'wp_notices'       => $this->wp_notices,
        ]);
    }

    /**
     * Fetch all WordPress notices for rendering.
     * @return string
     */
    private function get_wp_notices(): string
    {
        ob_start();
        do_action('admin_notices');
        do_action('all_admin_notices');
        $notices = ob_get_clean();

        return $notices ?: '';
    }

    /**
     * Disable auto-injection of WordPress notices.
     * @return void
     */
    private function disable_auto_injection(): void
    {
        global $wp_filter;

        // Ensure $wp_filter is defined and hooks are available
        if (isset($wp_filter['admin_notices']) && is_a($wp_filter['admin_notices'], 'WP_Hook')) {
            $wp_filter['admin_notices']->callbacks = [];
        }

        if (isset($wp_filter['all_admin_notices']) && is_a($wp_filter['all_admin_notices'], 'WP_Hook')) {
            $wp_filter['all_admin_notices']->callbacks = [];
        }
    }

}
