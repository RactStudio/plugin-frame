<?php

namespace PluginFrame\Providers;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

use PluginFrame\Services\Enqueue;

class Enqueues
{
    protected $enqueueFile;

    public function __construct()
    {
        $this->enqueueFile = new Enqueue();

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
        // $this->enqueueFile->registerFrontendStyle(
        //     'plugin-frame-frontend-tailwind-styles',
        //     PLUGIN_FRAME_URL . 'resources/assets/css/tailwind.min.css',
        //     [],
        //     false,
        //     'all',
        // );

        // Frontend styles
        $this->enqueueFile->registerFrontendStyle(
            'plugin-frame-frontend-styles',
            PLUGIN_FRAME_URL . 'resources/assets/css/frontend.min.css',
            [],
            PLUGIN_FRAME_VERSION,
            'all',
        );

        // Frontend AlpineJS script
        $this->enqueueFile->registerFrontendScript(
            'plugin-frame-frontend-alpine-script',
            PLUGIN_FRAME_URL . 'resources/assets/js/alpine.min.js',
            [],
            false,
            true,
            null,
            [
                'type' => 'module',
                'defer' => '',
            ],
        );
        
        // // Frontend Flowbite script
        // $this->enqueueFile->registerFrontendScript(
        //     'plugin-frame-frontend-flowbite-script',
        //     PLUGIN_FRAME_URL . 'resources/assets/js/flowbite.min.js',
        //     [],
        //     false,
        //     true,
        //     null,
        // );

        // // Frontend Lucide script
        // $this->enqueueFile->registerFrontendScript(
        //     'plugin-frame-frontend-lucide-script',
        //     PLUGIN_FRAME_URL . 'resources/assets/js/lucide.min.js',
        //     [],
        //     false,
        //     true,
        //     null,
        // );

        // Frontend Main Scripts (Auto generated)
        $this->enqueueFile->registerFrontendScript(
            'plugin-frame-frontend-main-script',
            PLUGIN_FRAME_URL . 'resources/assets/js/frontend.min.js',
            [],
            PLUGIN_FRAME_VERSION,
            true,
            null,
        );

        // // Example with condition
        // $this->enqueueFile->registerFrontendScript(
        //     'plugin-frame-conditional-frontend-script',
        //     PLUGIN_FRAME_URL . 'resources/assets/js/frontend-conditional.js',
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
        $this->enqueueFile->registerAdminStyle(
            'plugin-frame-admin-tailwind-styles',
            PLUGIN_FRAME_URL . 'resources/assets/css/tailwind.min.css',
            [],
            false,
            'all',
        );

        // Admin styles
        $this->enqueueFile->registerAdminStyle(
            'plugin-frame-admin-styles',
            PLUGIN_FRAME_URL . 'resources/assets/css/admin.min.css',
            ['plugin-frame-admin-tailwind-styles'],
            PLUGIN_FRAME_VERSION,
            'all',
        );

        // Admin AlpineJS script
        $this->enqueueFile->registerAdminScript(
            'plugin-frame-admin-alpine-script',
            PLUGIN_FRAME_URL . 'resources/assets/js/alpine.min.js',
            [],
            false,
            true,
            null,
            [
                'type' => 'module',
                'defer' => '',
            ],
        );

        // Admin Flowbite script
        $this->enqueueFile->registerAdminScript(
            'plugin-frame-admin-flowbite-script',
            PLUGIN_FRAME_URL . 'resources/assets/js/flowbite.min.js',
            [],
            false,
            true,
            null,
        );

        // Admin Lucide script
        $this->enqueueFile->registerAdminScript(
            'plugin-frame-admin-lucide-script',
            PLUGIN_FRAME_URL . 'resources/assets/js/lucide.min.js',
            [],
            false,
            true,
            null,
        );

        // Admin Main Scripts (Auto generated)
        $this->enqueueFile->registerAdminScript(
            'plugin-frame-admin-main-script',
            PLUGIN_FRAME_URL . 'resources/assets/js/admin.min.js',
            [],
            PLUGIN_FRAME_VERSION,
            true,
            null,
        );

        // // Example with condition
        // $this->enqueueFile->registerAdminScript(
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
