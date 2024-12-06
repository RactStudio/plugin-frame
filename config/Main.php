<?php

namespace PluginFrame;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Main
{
    public function __construct()
    {
        // Check PHP version and load the plugin if requirements are met.
        add_action('plugins_loaded', [$this, 'load_with_php_version_check'], 1);
    }

    // Load the plugin with minimum PHP version requirment check
    public function load_with_php_version_check(): void
    {
        // Check minimum PHP version required for the plugin.
        if (version_compare(PHP_VERSION, PLUGIN_FRAME_MIN_PHP, '<')) {
            add_action('admin_notices', function (): void
            {
                ?>
                <div class="notice notice-error">
                    <p>
                        <?php 
                        echo esc_html(
                            PLUGIN_FRAME_NAME . ' requires PHP version ' . PLUGIN_FRAME_MIN_PHP . 
                            ' or higher. Please update your PHP version to continue using this plugin.'
                        ); 
                        ?>
                    </p>
                </div>
                <?php
            });
        } else {
            // Load the plugin functionality.
            $this->plugin_frame_init();
        }
    }
    
    /**
     * Load files and initialize the plugin.
     */
    private function plugin_frame_init(): void
    {
        // Fires when the plugin starts loading
        do_action( 'plugin_frame_load_start' );

        // Load composer vendor files
        $this->load_composer_vendor();

        // Load plugin debugeer
        $this->load_plugin_frame_debug();

        // Load plugin default features priority first
        $this->load_plugin_frame_config('first');

        // Load framework files to load classes
        $this->load_directories_files();

        // Load plugin Providers Classes
        $this->load_providers_classes();

        // Load plugin Routes Classes
        $this->load_routes_base_classes();

        // Load plugin Api Classes
        $this->load_api_base_classes();

        // Load plugin default features priority last
        $this->load_plugin_frame_config('last');

        // Fires when the plugin finishes loading completely
        do_action( 'plugin_frame_load_end' );
    }

    // Load plugin debugeer files
    public function load_plugin_frame_debug(): void
    {
        // Load Debugger helper file to load Class
        require_once PLUGIN_FRAME_DIR . 'pf/debug/Helpers.php';
    }

    // Load plugin default features priority first or last
    private function load_plugin_frame_config($priority): void
    {
        // Load the config file
        require_once PLUGIN_FRAME_DIR . 'config/config.php';

        if ($priority === 'first') {
            // Load plugin default features priority first
            (new \PluginFrame\Config())->priority_load_first();
        } elseif ($priority === 'last') {
            // Load plugin default features priority last
            (new \PluginFrame\Config())->priority_load_last();
        }
    }

    // Load composer vendor files
    private function load_composer_vendor(): void
    {
        // Fires when the composer started loading
        do_action( 'plugin_frame_load_composer_start' );

        // Autoload Composer dependencies
        if ( file_exists( PLUGIN_FRAME_DIR . 'vendor/autoload.php' ) )
        {
            require_once PLUGIN_FRAME_DIR . 'vendor/autoload.php';
        }
        
        // Fires when the composer finishes loading
        do_action( 'plugin_frame_load_composer_end' );
    }

    // Load PHP files from directories and subdirectories
    private function load_directories_files(): void
    {
        // Directories to scan and load PHP files (use main directories)
        $directories = [
            PLUGIN_FRAME_DIR . 'app/',        // App-related files (Controllers/Helpers, Models, Providers, Services, etc.)
            PLUGIN_FRAME_DIR . 'resources/',  // Public assets, or other PHP files (if any)
            PLUGIN_FRAME_DIR . 'languages/',  // Language files for i18n
        ];

        // Loop through the directories and load files recursively
        foreach ($directories as $directory)
        {
            $this->load_files_recursively($directory);  // Calls the function to process each directory
        }
    }

    // Recursive function to load PHP files from directories and subdirectories
    private function load_files_recursively($dir): void
    {
        // Load all PHP files in the current directory
        foreach (glob($dir . '/*.php') as $file)
        {
            require_once $file;
        }

        // Recursively load PHP files from subdirectories
        foreach (glob($dir . '/*', GLOB_ONLYDIR) as $subdir)
        {
            $this->load_files_recursively($subdir);  // Calls itself for each subdirectory
        }
    }
    
    // Load plugin Routes Classes [app/Routes/RoutesBase.php]
    private function load_routes_base_classes(): void
    {
        // Fires when the plugin started loading classes
        do_action( 'plugin_frame_load_routes_classes_start' );

        // Load classes to load WordPress Routes
        new \PluginFrame\Routes\RoutesBase();
        
        // Fires when the plugin finishes loading classes
        do_action( 'plugin_frame_load_routes_classes_end' );
    }
    
    // Load plugin Api Classes [app/Api/ApiBase.php]
    private function load_api_base_classes(): void
    {
        // Fires when the plugin finishes loading classes
        do_action( 'plugin_frame_load_api_classes_start' );

        // Load classes to load framework files
        new \PluginFrame\Api\ApiBase();
        
        // Fires when the plugin finishes loading classes
        do_action( 'plugin_frame_load_api_classes_end' );
    }

    // Load plugin Providers Classes [app/Providers/LoadProviders.php]
    private function load_providers_classes(): void
    {
        // Fires when the plugin finishes loading classes
        do_action( 'plugin_frame_load_providers_classes_start' );

        // Load classes to load framework files
        new \PluginFrame\LoadProviders();
        
        // Fires when the plugin finishes loading classes
        do_action( 'plugin_frame_load_providers_classes_end' );
    }
}