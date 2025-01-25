<?php 

namespace PluginFrame\Providers;

use PluginFrame\Helpers\Admin\Dashboard;
use PluginFrame\Helpers\Admin\Settings;
use PluginFrame\Helpers\Admin\Tools;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Menus
{
    protected $menusService;

    /**
     * Initialize the menus
     */
    public function __construct()
    {
        $this->menusService = new \PluginFrame\Services\Menus();
        $this->registerMenus();
    }

    /**
     * Register plugin menus and submenus.
     *
     * @return void
     */
    protected function registerMenus(): void
    {
        // Register the main menu page (Dashboard)
        $dashboard = new Dashboard();
        $this->menusService->addMenuPage(
            'Plugin Frame Dashboard',       // Page title
            'Plugin Frame',                 // Menu title
            'manage_options',              // Capability
            'plugin-frame',                  // Menu slug
            [$dashboard, 'render'],          // Callback method
            'dashicons-grid-view',  // Icon
            2                                // Position
        );

        // Register a submenu page (Settings)
        $settings = new Settings();
        $this->menusService->addSubmenuPage(
            'plugin-frame',                // Parent menu slug
            'Plugin Frame Settings',        // Submenu page title
            'Settings',                     // Submenu title
            'manage_options',              // Capability
            'pf-settings',                   // Submenu slug
            [$settings, 'render']            // Callback method
        );

        // Register a submenu page (Tools)
        $tools = new Tools();
        $this->menusService->addSubmenuPage(
            'plugin-frame',                // Parent menu slug
            'Plugin Frame Tools',        // Submenu page title
            'Tools',                     // Submenu title
            'manage_options',              // Capability
            'pf-tools',                   // Submenu slug
            [$tools, 'render']            // Callback method
        );

        // Add more menus or submenus here as needed

    }
}
