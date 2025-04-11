<?php

namespace PluginFrame\Hooks;

// Exit if accessed directly
if (!defined('ABSPATH')) { exit; }

require_once PLUGIN_FRAME_DIR . 'app/Core/Hooks/Uninstall.php';
use PluginFrame\Core\Hooks\Uninstall as CoreUninstall;

class Uninstall extends CoreUninstall
{
    protected static function uninstallCore(): void
    {
        parent::uninstallCore();
        // static::cleanupDatabase();
        // static::removePluginOptions();
    }

    protected static function cleanupDatabase(): void
    {
        global $wpdb;
        $tableName = $wpdb->prefix . 'example_table';
        $wpdb->query("DROP TABLE IF EXISTS $tableName");
    }

    protected static function removePluginOptions(): void
    {
        delete_option('plugin_frame_version');
        delete_option('plugin_frame_settings');
    }
}