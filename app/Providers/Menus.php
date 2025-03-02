<?php 

namespace PluginFrame\Providers;

use PluginFrame\Services\Menus as MenusService;

// Standard JS based admin pages
use PluginFrame\Views\Admin\Dashboard;
use PluginFrame\Views\Admin\Settings;
use PluginFrame\Views\Admin\Tools;
// Alpine JS based admin pages
use PluginFrame\Views\AdminAlpine\DashboardAlpine;
use PluginFrame\Views\AdminAlpine\SettingsAlpine;
use PluginFrame\Views\AdminAlpine\ToolsAlpine;

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
        $this->menusService = new MenusService();
        $this->registerMenus();
    }

    /**
     * Register plugin menus and submenus.
     *
     * @return void
     */
    protected function registerMenus(): void
    {
        /**
         * JavaScript based standard admin pages
         */

        // Register the main menu page (Dashboard)
        $this->menusService->addMenuPage(
            'Plugin Frame Dashboard',       // Page title
            'Plugin Frame',                 // Menu title
            'manage_options',              // Capability
            'plugin-frame',                  // Menu slug
            [new Dashboard(), 'render'],     // Callback method
            'dashicons-grid-view',            // Icon
            3                                // Position
        );

        // Register a submenu page (Settings)
        $this->menusService->addSubmenuPage(
            'plugin-frame',                // Parent menu slug
            'Plugin Frame Settings',        // Submenu page title
            'Settings',                     // Submenu title
            'manage_options',              // Capability
            'pf-settings',                   // Submenu slug
            [new Settings(), 'render']            // Callback method
        );

        // Register a submenu page (Tools)
        $this->menusService->addSubmenuPage(
            'plugin-frame',                // Parent menu slug
            'Plugin Frame Tools',           // Submenu page title
            'Tools',                        // Submenu title
            'manage_options',              // Capability
            'pf-tools',                      // Submenu slug
            [new Tools(), 'render']               // Callback method
        );

        /**
         * Alpine JS based admin pages
         */
        
        // Register the main menu page (DashboardAlpine)
        $this->menusService->addMenuPage(
            'Plugin Frame Dashboard',       // Page title
            'Plugin Frame',                 // Menu title
            'manage_options',              // Capability
            'plugin-frame-alpine',                  // Menu slug
            [new DashboardAlpine(), 'render'],     // Callback method
            'dashicons-image-filter',            // Icon
            3                                // Position
        );

        // Register a submenu page (SettingsAlpine)
        $this->menusService->addSubmenuPage(
            'plugin-frame-alpine',                // Parent menu slug
            'Plugin Frame Settings',        // Submenu page title
            'Settings',                     // Submenu title
            'manage_options',              // Capability
            'pf-settings-alpine',                   // Submenu slug
            [new SettingsAlpine(), 'render']            // Callback method
        );

        // Register a submenu page (ToolsAlpine)
        $this->menusService->addSubmenuPage(
            'plugin-frame-alpine',                // Parent menu slug
            'Plugin Frame Tools',           // Submenu page title
            'Tools',                        // Submenu title
            'manage_options',              // Capability
            'pf-tools-alpine',                      // Submenu slug
            [new ToolsAlpine(), 'render']               // Callback method
        );

        // Add more menus or submenus here as needed

    }
}
