<?php

namespace PluginFrame\Providers;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

use PluginFrame\Services\Enqueue;

class EnqueueAssets
{
    protected $enqueueFiles;

    public function __construct()
    {
        $this->enqueueFiles = new Enqueue();

        $this->registerFrontendAssets();
        $this->registerAdminAssets();
    }

    /**
     * Examples of conditions:
     * Conditions are Optional
     */

    // // Only load on single post pages
    // return is_single();

    // // Only load on specific pages (use page slugs or IDs)
    // return is_page(['custom-template', 42]);

    // // Only load on archive pages (categories, tags, custom post types, etc.)
    // return is_archive();

    // // Only load on the home page
    // return is_home() || is_front_page();

    // // Only load on WooCommerce product pages
    // return function_exists('is_product') && is_product();

    // // Only load for logged-in users
    // return is_user_logged_in();

    // // Only load on specific admin pages (e.g., settings or custom post types)
    // return isset($_GET['page']) && $_GET['page'] === 'plugin-settings';

    // // Only load on specific post types in the admin area
    // return function_exists('get_current_screen') && get_current_screen()->post_type === 'custom_post_type';

    /**
     * Register all frontend scripts and styles.
     */
    protected function registerFrontendAssets()
    {
        // // Frontend Tailwind styles
        // $this->enqueueFiles->registerFrontendStyle(
        //     'plugin-frame-frontend-tailwind-styles',
        //     PLUGIN_FRAME_URL . 'resources/assets/css/tailwind.min.css',
        //     [],
        //     PLUGIN_FRAME_VERSION,
        //     false
        // );

        // Frontend styles
        $this->enqueueFiles->registerFrontendStyle(
            'plugin-frame-frontend-styles',
            PLUGIN_FRAME_URL . 'resources/assets/css/frontend.css',
            [],
            PLUGIN_FRAME_VERSION,
            false
        );

        // Frontend AlpineJS script
        $this->enqueueFiles->registerFrontendScript(
            'plugin-frame-frontend-alpine-script',
            PLUGIN_FRAME_URL . 'resources/assets/js/alpinejs.min.js',
            [],
            '3.14.3', // Current version at the time - use latest version (if needed)
            true
        );

        // Frontend AlpineJS Init script
        $this->enqueueFiles->registerFrontendScript(
            'plugin-frame-frontend-alpine-init-script',
            PLUGIN_FRAME_URL . 'resources/assets/js/alpine-init-frontend.js',
            ['plugin-frame-frontend-alpine-script'],
            PLUGIN_FRAME_VERSION,
            true
        );

        // // Frontend Tailwind script [Require Alpine JS]
        // $this->enqueueFiles->registerFrontendScript(
        //     'plugin-frame-frontend-tailwind-script',
        //     PLUGIN_FRAME_URL . 'resources/assets/js/tailwind.js',
        //     ['plugin-frame-frontend-alpine-init-script'],
        //     PLUGIN_FRAME_VERSION,
        //     true
        // );

        // Frontend scripts [Dependent on WP jQuery]
        $this->enqueueFiles->registerFrontendScript(
            'plugin-frame-frontend-script',
            PLUGIN_FRAME_URL . 'resources/assets/js/frontend.js',
            ['jquery'],
            PLUGIN_FRAME_VERSION,
            true
        );

        // // Example with condition
        // $this->enqueueFiles->registerFrontendScript(
        //     'plugin-frame-custom-frontend-script',
        //     PLUGIN_FRAME_URL . 'resources/assets/js/frontend.js',
        //     ['jquery'],
        //     PLUGIN_FRAME_VERSION,
        //     true,
        //     function () {
        //         // Load only on pages with the "custom-template" slug
        //         return is_page('custom-template');
        //     }
        // );

    }

    /**
     * Register all admin scripts and styles.
     */
    protected function registerAdminAssets()
    {
        // Admin Tailwind styles
        $this->enqueueFiles->registerAdminStyle(
            'plugin-frame-admin-tailwind-styles',
            PLUGIN_FRAME_URL . 'resources/assets/css/tailwind.min.css',
            [],
            PLUGIN_FRAME_VERSION,
            false
        );

        // Admin styles
        $this->enqueueFiles->registerAdminStyle(
            'plugin-frame-admin-styles',
            PLUGIN_FRAME_URL . 'resources/assets/css/admin.css',
            [],
            PLUGIN_FRAME_VERSION,
            false
        );

        // Admin AlpineJS script
        $this->enqueueFiles->registerAdminScript(
            'plugin-frame-admin-alpine-script',
            PLUGIN_FRAME_URL . 'resources/assets/js/alpinejs.min.js',
            [],
            '3.14.3',
            true
        );

        // Admin AlpineJS script
        $this->enqueueFiles->registerAdminScript(
            'plugin-frame-admin-alpine-init-script',
            PLUGIN_FRAME_URL . 'resources/assets/js/alpine-init-admin.js',
            ['plugin-frame-admin-alpine-script'],
            PLUGIN_FRAME_VERSION,
            true
        );

        // // Admin Tailwind script [Dependent on Alpine Init JS]
        // $this->enqueueFiles->registerAdminScript(
        //     'plugin-frame-admin-tailwind-script',
        //     PLUGIN_FRAME_URL . 'resources/assets/js/tailwind.js',
        //     ['plugin-frame-admin-alpine-init-script'],
        //     PLUGIN_FRAME_VERSION,
        //     true
        // );

        // Admin scripts [Dependent on WP jQuery]
        $this->enqueueFiles->registerAdminScript(
            'plugin-frame-admin-script',
            PLUGIN_FRAME_URL . 'resources/assets/js/admin.js',
            ['jquery'],
            PLUGIN_FRAME_VERSION,
            true
        );

        // // Example with condition
        // $this->enqueueFiles->registerAdminScript(
        //     'admin-conditional-script',
        //     PLUGIN_FRAME_URL . 'resources/assets/js/admin-conditional.js',
        //     [],
        //     PLUGIN_FRAME_VERSION,
        //     true,
        //     function () {
        //         // Only load on post editing screens
        //         return function_exists('get_current_screen') && get_current_screen()->base === 'post';
        //     }
        // );

    }
}
