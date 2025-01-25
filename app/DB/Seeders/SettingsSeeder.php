<?php

namespace Pluginframe\DB\Seeders;

use Pluginframe\DB\Seeders\Seeder;

/**
 * SettingsSeeder to seed plugin-specific settings.
 */
class SettingsSeeder extends Seeder
{
    /**
     * Run the settings seeder.
     */
    public function run()
    {
        // Seed plugin version if it doesn't exist
        if (!get_option('plugin_frame_version')) {
            update_option('plugin_frame_version', '1.0');
        }

        // Seed plugin settings if not already set
        if (!get_option('plugin_frame_settings')) {
            update_option('plugin_frame_settings', ['key' => 'value']);
        }
    }
}
