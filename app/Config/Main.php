<?php

namespace PluginFrame\Config;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Main {

    public function __construct()
    {
        // Perform PHP version check early before plugin execution.
        if ( ! $this->is_php_version_compatible() )
        {
            return; // Stop plugin execution if PHP version requirement is not met.
        }

        // Register activation and deactivation hooks dynamically.
        $this->on_plugin_activation();
        $this->on_plugin_deactivation();

        // Initialize plugin functionalities after all plugins are loaded.
        add_action('plugins_loaded', [$this, 'initialize_plugin']);
    }

    /**
     * Check for minimum PHP version compatibility.
     * 
     * @return bool True if compatible, otherwise false.
     */
    private function is_php_version_compatible(): bool
    {
        if ( version_compare(PHP_VERSION, PLUGIN_FRAME_MIN_PHP, '<') ) {
            add_action('admin_notices', [$this, 'php_version_notice']);
            return false;
        }
        return true;
    }

    /**
     * Show admin notice for incompatible PHP version.
     */
    public function php_version_notice(): void
    {
        ?>
        <div class="notice notice-error">
            <p>
                <?php 
                echo esc_html(
                    PLUGIN_FRAME_NAME . ' requires PHP version ' . PLUGIN_FRAME_MIN_PHP . 
                    ' or higher. Please update your PHP version to use this plugin.'
                ); 
                ?>
            </p>
        </div>
        <?php
    }

    /**
     * Register tasks on plugin activation.
     */
    public function on_plugin_activation(): void
    {
        $activation_hook = PLUGIN_FRAME_DIR . 'app/Hooks/Activation.php';
        if ( file_exists($activation_hook) )
        {
            require_once $activation_hook;
            new \PluginFrame\Hooks\Activation();
        } else {
            error_log('Activation hook not found:: '. $activation_hook);
        }
    }

    /**
     * Register tasks on plugin deactivation.
     */
    public function on_plugin_deactivation(): void
    {
        $deactivation_hook = PLUGIN_FRAME_DIR . 'app/Hooks/Deactivation.php';
        if ( file_exists($deactivation_hook) )
        {
            require_once $deactivation_hook;
            new \PluginFrame\Hooks\Deactivation();
        } else {
            error_log('Deactivation hook not found:: '. $deactivation_hook);
        }
    }

    /**
     * Initialize the plugin: load dependencies, configurations, and features.
     */
    public function initialize_plugin(): void
    {
        do_action('plugin_frame_load_start');

        // Load all required files and features.
        $this->load_dependencies();
        $this->load_features();

        do_action('plugin_frame_load_end');
    }

    /**
     * Load all dependencies required for the plugin.
     */
    private function load_dependencies(): void
    {
        $this->load_composer_autoload();
        $this->load_directory_files([
            PLUGIN_FRAME_DIR . 'app/',       // Main app files.
            PLUGIN_FRAME_DIR . 'resources/', // Resources and assets.
            PLUGIN_FRAME_DIR . 'languages/', // Language files.
        ]);
    }

    /**
     * Load Composer autoload file.
     */
    private function load_composer_autoload(): void
    {
        $composer_autoload = PLUGIN_FRAME_DIR . 'vendor/autoload.php';
        if ( file_exists($composer_autoload) ) {
            require_once $composer_autoload;
        }
    }

    /**
     * Recursively load PHP files from given directories.
     *
     * @param array $directories List of directories to scan.
     */
    private function load_directory_files(array $directories): void
    {
        foreach ($directories as $directory) {
            $this->load_files_recursively($directory);
        }
    }

    /**
     * Helper to recursively include PHP files.
     *
     * @param string $dir Directory path.
     */
    private function load_files_recursively(string $dir): void
    {
        foreach (glob($dir . '/*.php') as $file) {
            require_once $file;
        }

        foreach (glob($dir . '/*', GLOB_ONLYDIR) as $subdir)
        {
            $this->load_files_recursively($subdir);
        }
    }

    /**
     * Load plugin-specific features.
     */
    private function load_features(): void
    {
        // Load debugging utilities.
        $this->load_debugger();

        (new \PluginFrame\Config\Config())->priority_load_first();

        // Initialize plugin routes and APIs.
        new \PluginFrame\Routes\Register();
        new \PluginFrame\Config\APIbase();

        // Initialize service providers and configuration.
        new \PluginFrame\Config\Providers();

        (new \PluginFrame\Config\Config())->priority_load_last();
    }

    /**
     * Load debugging utilities if files exist.
     */
    private function load_debugger(): void
    {
        $debug_helper = PLUGIN_FRAME_DIR . 'app/Utilities/Debug/Helpers.php';
        if ( file_exists($debug_helper) )
        {
            require_once $debug_helper;
        } else {
            pf_log('Debug helper not found, and couldn\'t be loaded.');
        } 

        $log_helper = PLUGIN_FRAME_DIR . 'app/Utilities/PFlogs/Helpers.php';
        if ( file_exists($log_helper) )
        {
            require_once $log_helper;
        } else {
            pf_log('PF Log helper not found, and couldn\'t be loaded.');
        }
    }

}
