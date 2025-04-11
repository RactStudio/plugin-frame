<?php

namespace PluginFrame\Core\Hooks;

// Exit if accessed directly
if (!defined('ABSPATH')) { exit; }

use Exception;

class Uninstall
{
    public function __construct()
    {
        register_uninstall_hook(PLUGIN_FRAME_FILE, [static::class, 'handleUninstall']);
    }

    public static function handleUninstall(): void
    {
        do_action('plugin_frame_pre_uninstall');
        
        try {
            static::uninstallCore();
            do_action('plugin_frame_post_uninstall');
            error_log(PLUGIN_FRAME_NAME . ' Uninstalled successfully.');
        } catch (Exception $e) {
            error_log(PLUGIN_FRAME_NAME . ' Uninstallation failed: ' . $e->getMessage());
            wp_die(__('Plugin uninstall failed. Check error logs.', 'plugin-frame'));
        }
    }

    protected static function uninstallCore(): void
    {
        do_action('plugin_frame_on_uninstall');
    }

    protected static function cleanupDatabase(): void
    {
        // Optional core database cleanup
    }

    protected static function removePluginOptions(): void
    {
        // Optional core options removal
    }
}