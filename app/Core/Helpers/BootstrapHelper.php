<?php

namespace PluginFrame\Core\Helpers;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

class BootstrapHelper
{
    /**
     * Check for minimum PHP version compatibility.
     * 
     * @return bool True if compatible, otherwise false.
     */
    public function is_php_version_compatible(): bool
    {
        if ( ! defined('PLUGIN_FRAME_MIN_PHP') ) {
            define('PLUGIN_FRAME_MIN_PHP', '7.4');
        }
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

    private function load_dependencies(): void
    {
        // Load composer autoloader.
        $this->load_composer_autoload();
        
        // Replace load_directory_files with autoloader registration
        spl_autoload_register([$this, 'autoload_classes']);
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
     * Autoload classes from specific directories using PSR-4-like structure.
     */
    public function autoload_classes(string $class): void
    {
        $prefix = 'PluginFrame';
        $prefix = $prefix.'\\';
        $base_dir = PLUGIN_FRAME_DIR . 'app/';
    
        // Check if the class uses the namespace prefix
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }
    
        // Get the relative class name and file path
        $relative_class = substr($class, $len);
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
        // Include the file if it exists
        if (file_exists($file)) {
            require_once $file;
        }
    }

    /**
     * Load plugin-specific features.
     */
    private function load_features(): void
    {
        (new \PluginFrame\Config\Config())->priority_load_first();

        // Initialize service providers and configuration.
        new \PluginFrame\Config\Providers();
        new \PluginFrame\Config\HooksLoader();

        // Initialize plugin routes and APIs.
        new \PluginFrame\Core\Routes\Register();
        new \PluginFrame\Config\APIbase();

        (new \PluginFrame\Config\Config())->priority_load_last();
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
     * Register tasks on plugin uninstall.
     */
    public function on_plugin_uninstall(): void
    {
        $uninstall_hook = PLUGIN_FRAME_DIR . 'app/Hooks/Uninstall.php';
        if ( file_exists($uninstall_hook) )
        {
            require_once $uninstall_hook;
            new \PluginFrame\Hooks\Uninstall();
        } else {
            error_log('Uninstall hook not found:: '. $uninstall_hook);
        }
    }

    /**
     * Register tasks on plugin upgrade.
     */
    public function on_plugin_upgrade(): void
    {
        $upgrade_hook = PLUGIN_FRAME_DIR . 'app/Hooks/Upgrade.php';
        if ( file_exists($upgrade_hook) )
        {
            require_once $upgrade_hook;
            new \PluginFrame\Hooks\Upgrade();
        } else {
            error_log('Upgrade hook not found:: '. $upgrade_hook);
        }
    }

    /**
     * Register tasks on plugin update.
     */
    public function on_plugin_updater(): void
    {
        $updater_hook = PLUGIN_FRAME_DIR . 'app/Config/Updater.php';
        if ( file_exists($updater_hook) )
        {
            require_once $updater_hook;
            new \PluginFrame\Config\Updater();
        } else {
            error_log('Updater config not found. File:: '. $updater_hook);
        }
    }

}