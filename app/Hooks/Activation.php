<?php

namespace PluginFrame\Hooks;

// Exit if accessed directly
if (!defined('ABSPATH')) { exit; }

require_once PLUGIN_FRAME_DIR . 'app/Core/Hooks/Activation.php';
use PluginFrame\Core\Hooks\Activation as CoreActivation;

class Activation extends CoreActivation
{
    protected function activate(): void
    {
        parent::activate(); // Preserve core hooks

        // $this->setupDatabase();
        // $this->setupInitialOptions();
    }

    protected function setupDatabase(): void
    {
        global $wpdb;
        $tableName = $wpdb->prefix . 'example_table';
        
        $sql = "CREATE TABLE IF NOT EXISTS $tableName (
            id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ".$wpdb->get_charset_collate().";";

        require_once ABSPATH.'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    protected function setupInitialOptions(): void
    {
        if (!get_option('plugin_frame_default_option')) {
            add_option('plugin_frame_default_option', 'default_value');
        }
    }
}