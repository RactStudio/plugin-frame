<?php

namespace PluginFrame\Hooks;

// Exit if accessed directly
if (!defined('ABSPATH')) { exit; }

class Upgrade
{
    /**
     * Constructor to register all hooks for upgrade and upload processes.
     */
    public function __construct()
    {
        // Register hooks for the upgrade process
        add_action('upgrader_process_complete', [$this, 'onUpgradeProcessComplete'], 10, 2);

        // Register hooks for the upload process
        add_filter('upgrader_pre_install', [$this, 'beforeUpload'], 10, 2);
        add_filter('upgrader_post_install', [$this, 'afterUpload'], 10, 2);

        // Example hooks for developers
        // $this->exampleUpgrade();
    }

    /**
     * Execute actions during the plugin upgrade/update process.
     *
     * @param \WP_Upgrader $upgrader The upgrader instance.
     * @param array        $options Options for the upgrader.
     */
    public function onUpgradeProcessComplete($upgrader, $options): void
    {
        if (isset($options['type']) && $options['type'] === 'plugin') {
            if (isset($options['plugins']) && is_array($options['plugins'])) {
                if (in_array(PLUGIN_FRAME_BASENAME, $options['plugins'], true)) {
                    error_log(PLUGIN_FRAME_NAME . ' upgrade process started.');

                    // Perform upgrade tasks
                    $this->performUpgradeTasks();

                    error_log(PLUGIN_FRAME_NAME . ' upgraded successfully.');
                }
            }

            // Allow developers to hook into the process completion
            do_action('plugin_frame_on_plugin_process_complete', $upgrader, $options);
        }
    }

    /**
     * Perform tasks during plugin upgrade.
     */
    private function performUpgradeTasks(): void
    {
        $currentVersion = get_option('plugin_frame_version', '1.0.0'); // Default version
        $newVersion = PLUGIN_FRAME_VERSION; // Replace with your plugin's constant or logic

        if (version_compare($currentVersion, $newVersion, '<')) {
            $this->updateDatabaseSchema();

            // Update the version in the database
            update_option('plugin_frame_version', $newVersion);

            error_log(PLUGIN_FRAME_NAME . " upgraded from version $currentVersion to $newVersion.");
        }
    }

    /**
     * Execute actions before a plugin is uploaded or replaced.
     *
     * @param bool   $return Default false, allowing upload to proceed.
     * @param array  $hookExtra Extra data passed by the upgrader.
     * @return bool
     */
    public function beforeUpload($return, $hookExtra)
    {
        if (isset($hookExtra['type']) && $hookExtra['type'] === 'plugin') {
            error_log('Before Upload Hook Triggered.');

            // Developers can add custom logic
            do_action('plugin_frame_before_plugin_upload', $hookExtra);
        }

        return $return;
    }

    /**
     * Execute actions after a plugin is uploaded or replaced.
     *
     * @param bool  $response Response data from the upgrader.
     * @param array $hookExtra Extra data passed by the upgrader.
     * @return bool
     */
    public function afterUpload($response, $hookExtra)
    {
        if (isset($hookExtra['type']) && $hookExtra['type'] === 'plugin') {
            error_log('After Upload Hook Triggered.');

            // Developers can add custom logic
            do_action('plugin_frame_after_plugin_upload', $response, $hookExtra);
        }

        return $response;
    }

    /**
     * Update database schema or perform other upgrade-specific logic.
     */
    private function updateDatabaseSchema(): void
    {
        // global $wpdb;
        // Example: Run custom database migration logic
        // $wpdb->query("ALTER TABLE {$wpdb->prefix}example_table ADD COLUMN new_column VARCHAR(255)");
    }

    /**
     * Example hooks for developers.
     */
    private function exampleUpgrade()
    {
        // Example: Hook into 'plugin_frame_before_plugin_upload'
        add_action('plugin_frame_before_plugin_upload', function ($hookExtra) {
            if (!empty($hookExtra['plugin']) && strpos($hookExtra['plugin'], 'restricted') !== false) {
                error_log('Blocked upload of restricted plugin.');
                wp_die(__('You cannot upload this plugin.', 'plugin-frame'));
            }
        });

        // Example: Hook into 'plugin_frame_after_plugin_upload'
        add_action('plugin_frame_after_plugin_upload', function ($response, $hookExtra) {
            error_log('Plugin uploaded successfully: ' . print_r($hookExtra, true));
        });

        // Example: Hook into 'plugin_frame_on_plugin_process_complete'
        add_action('plugin_frame_on_plugin_process_complete', function ($upgrader, $hookExtra) {
            if (isset($hookExtra['plugin']) && $hookExtra['plugin'] === 'specific-plugin/specific-plugin.php') {
                error_log('Completed process for specific plugin.');
            }
        });
    }
}
