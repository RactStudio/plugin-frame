<?php

namespace PluginFrame\Config;

use PluginFrame\Core\Services\Container;
use PluginFrame\Providers\Options\OptionsProvider;
use PluginFrame\Core\Services\Options\Interfaces\OptionStorageInterface;
use PluginFrame\Core\Services\Options\WPOptionStorage;
use PluginFrame\Core\Services\Options\CustomTableOption;
use PluginFrame\Core\Services\Options\OptionManager;

// Exit if accessed directly
if (! defined('ABSPATH')) {
    exit;
}

class Options
{
    public function __construct()
    {
        $this->pf_options_container_loader();
        $this->registerActivationHook();
    }

    /**
     * Bind all option services into the container.
     */
    private function pf_options_container_loader(): void
    {
        // WP options adapter
        Container::bind(
            OptionStorageInterface::class . ':wp',
            fn($c) => new WPOptionStorage()
        );

        // Custom table adapter
        Container::bind(
            OptionStorageInterface::class . ':custom',
            fn($c) => new CustomTableOption()
        );

        // Central manager
        Container::bind(
            OptionManager::class,
            fn($c) => new OptionManager($c)
        );

        // Default storage (WP)
        Container::bind(
            OptionStorageInterface::class,
            fn($c) => $c->get(OptionStorageInterface::class . ':wp')
        );

        // Your OptionsProvider (will be resolved via DI)
        Container::bind(
            OptionsProvider::class,
            fn($c) => new OptionsProvider($c->get(OptionManager::class))
        );
    }

    /**
     * Create the custom options table on plugin activation.
     */
    private function registerActivationHook(): void
    {
        // Assumes the main plugin file lives at PLUGIN_FRAME_DIR . 'plugin-frame.php'
        // register_activation_hook(
        //     PLUGIN_FRAME_DIR . 'plugin-frame.php',
        //     [self::class, 'createOptionsTable']
        // );
        $this->createOptionsTable();
    }

    /**
     * Activation callback: ensures our 'pf_options' table exists.
     */
    public static function createOptionsTable(): void
    {
        global $wpdb;

        $table      = $wpdb->prefix . 'pf_options';
        $charset    = $wpdb->get_charset_collate();
        $sql        = "CREATE TABLE {$table} (
            option_key   varchar(191) NOT NULL,
            option_value longtext      NOT NULL,
            PRIMARY KEY  (option_key)
        ) {$charset};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        \dbDelta($sql);
    }
}
