<?php

namespace PluginFrame\Core\Services;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Menus
{
    /**
     * Register a menu page.
     *
     * @param string $pageTitle The title of the page.
     * @param string $menuTitle The title of the menu item.
     * @param string $capability The capability required to view this menu.
     * @param string $menuSlug A unique slug for the menu.
     * @param callable $callback The callback function to render the menu page.
     * @param string|null $iconUrl Optional. The URL of the menu icon.
     * @param int|null $position Optional. The position of the menu in the admin sidebar.
     * @return void
     */
    public function addMenuPage(
        string $pageTitle,
        string $menuTitle,
        string $capability,
        string $menuSlug,
        callable $callback,
        ?string $iconUrl = null,
        ?int $position = null
    ): void {
        add_action('admin_menu', function () use ($pageTitle, $menuTitle, $capability, $menuSlug, $callback, $iconUrl, $position) {
            add_menu_page($pageTitle, $menuTitle, $capability, $menuSlug, $callback, $iconUrl, $position);
        });
    }

    /**
     * Register a submenu page.
     *
     * @param string $parentSlug The slug of the parent menu.
     * @param string $pageTitle The title of the submenu page.
     * @param string $menuTitle The title of the submenu item.
     * @param string $capability The capability required to view this submenu.
     * @param string $menuSlug A unique slug for the submenu.
     * @param callable $callback The callback function to render the submenu page.
     * @return void
     */
    public function addSubmenuPage(
        string $parentSlug,
        string $pageTitle,
        string $menuTitle,
        string $capability,
        string $menuSlug,
        callable $callback
    ): void {
        add_action('admin_menu', function () use ($parentSlug, $pageTitle, $menuTitle, $capability, $menuSlug, $callback) {
            add_submenu_page($parentSlug, $pageTitle, $menuTitle, $capability, $menuSlug, $callback);
        });
    }
}
