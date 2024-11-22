<?php 

namespace PluginFrame\Providers;

use PluginFrame\Helpers\Admin\Dashboard;
use PluginFrame\Helpers\Admin\Settings;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Menus
{
    /**
     * Initialize the menus
     */
    public function __construct()
    {
        $this->registerMenus();
    }

    /**
     * Register plugin menus and submenus.
     *
     * @return void
     */
    protected function registerMenus(): void
    {
        $menusService = new \PluginFrame\Services\Menus();

        // Register the main menu page (Dashboard)
        $dashboard = new Dashboard();
        $menusService->addMenuPage(
            'Plugin Frame Dashboard',       // Page title
            'Plugin Frame',                 // Menu title
            'manage_options',              // Capability
            'plugin-frame',                  // Menu slug
            [$dashboard, 'render'],          // Callback method
            'dashicons-admin-home',           // Icon
            2                                // Position
        );

        // Register a submenu page (Settings)
        $settings = new Settings();
        $menusService->addSubmenuPage(
            'plugin-frame',                // Parent menu slug
            'Plugin Frame Settings',        // Submenu page title
            'Settings',                     // Submenu title
            'manage_options',              // Capability
            'pf-settings',                   // Submenu slug
            [$settings, 'render']            // Callback method
        );

        // Add more menus or submenus here as needed

    }
}
