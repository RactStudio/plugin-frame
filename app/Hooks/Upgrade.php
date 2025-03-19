<?php

namespace PluginFrame\Hooks;

// Exit if accessed directly
if (!defined('ABSPATH')) { exit; }

require_once PLUGIN_FRAME_DIR . 'app/Core/Hooks/Upgrade.php';
use PluginFrame\Core\Hooks\Upgrade as CoreUpgrade;

class Upgrade extends CoreUpgrade
{
    protected function updateDatabaseSchema(): void
    {
        // Custom database migrations
        global $wpdb;
        // $wpdb->query("ALTER TABLE {$wpdb->prefix}example_table ADD COLUMN new_column VARCHAR(255)");
    }

    protected function handleBeforeUpload(array $hookExtra): void
    {
        parent::handleBeforeUpload($hookExtra);
        // Custom pre-upload logic
    }

    protected function handleAfterUpload($response, array $hookExtra): void
    {
        parent::handleAfterUpload($response, $hookExtra);
        // Custom post-upload logic
    }
}
