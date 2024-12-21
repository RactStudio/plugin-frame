<?php

namespace PluginFrame\Hooks;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Upgrade
{
    /**
     * Constructor to register the upgrade logic.
     */
    public function __construct()
    {
        add_action('upgrader_process_complete', [$this, 'onUpgrade'], 10, 2);
    }

    /**
     * Logic executed during plugin upgrade/update.
     *
     * @param \WP_Upgrader $upgrader The upgrader instance.
     * @param array $options Options for the upgrader.
     */
    public function onUpgrade($upgrader, $options): void
    {
        // Check if the plugin is being upgraded
        if (isset($options['plugins']) && is_array($options['plugins'])) {
            // $pluginBasename = plugin_basename(PLUGIN_FRAME_FILE);
            if (in_array(PLUGIN_FRAME_BASENAME, $options['plugins'], true)) {
                // Log upgrade process
                error_log(PLUGIN_FRAME_NAME . ' upgrade process started.');

                // Perform upgrade tasks
                // $this->performUpgradeTasks();

                // Log success
                error_log(PLUGIN_FRAME_NAME . ' upgraded successfully.');
            }
        }
    }

    /**
     * Perform upgrade tasks.
     */
    private function performUpgradeTasks(): void
    {
        $currentVersion = get_option('plugin_frame_version', '1.0.0'); // Default version
        $newVersion = PLUGIN_FRAME_VERSION; // Replace with your plugin's constant or logic

        if (version_compare($currentVersion, $newVersion, '<')) {
            // Example: Perform database migrations or other tasks
            $this->updateDatabaseSchema();

            // Update the version in the database
            update_option('plugin_frame_version', $newVersion);

            error_log(PLUGIN_FRAME_NAME . " upgraded from version $currentVersion to $newVersion.");
        }
    }

    /**
     * Update database schema or other upgrade-specific logic.
     */
    private function updateDatabaseSchema(): void
    {
        global $wpdb;
        // Example: Run custom database migration logic
        // $wpdb->query("ALTER TABLE {$wpdb->prefix}example_table ADD COLUMN new_column VARCHAR(255)");
    }
}
