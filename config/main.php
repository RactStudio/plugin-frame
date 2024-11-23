<?php

namespace PluginFrame;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Main
{
    public function __construct()
    {
        // Check if the PHP version 7.4 or higher.
        if (version_compare(PHP_VERSION, '7.4', '<')) {
            wp_die("{PLUGIN_FRAME_NAME} requires PHP version 7.4 or higher.");
        }
        // Prioritize loading if needed
        add_action( 'plugins_loaded', [$this, 'plugin_frame_init'], 1 );
    }

    /**
     * Plugin initialization
     */
    public function plugin_frame_init(): void
    {
        // Fires when the plugin starts loading
        do_action( 'plugin_frame_load_start' );

        $this->pf_load_composer_vendor();

        // Load framework files to load classes
        $this->pf_load_directories_files();

        // Load plugin Routes Classes
        $this->pf_load_routes_base_classes();

        // Load plugin Api Classes
        $this->pf_load_api_base_classes();

        // Load plugin Providers Classes
        $this->pf_load_providers_classes();

        // Fires when the plugin finishes loading completely
        do_action( 'plugin_frame_load_end' );
    }

    private function pf_load_composer_vendor(): void
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
    private function pf_load_directories_files(): void
    {
        // Directories to scan and load PHP files (use main directories)
        $directories = [
            PLUGIN_FRAME_DIR . 'app/',        // App-related files (Controllers, Models, Services, etc.)
            PLUGIN_FRAME_DIR . 'resources/',  // Public assets or other PHP files (if any)
            PLUGIN_FRAME_DIR . 'languages/',  // Language files for i18n
            PLUGIN_FRAME_DIR . 'cli/',        // WP-CLI commands (optional)
        ];

        // Loop through the directories and load files recursively
        foreach ($directories as $directory)
        {
            $this->pf_load_files_recursively($directory);  // Calls the function to process each directory
        }
    }

    // Recursive function to load PHP files from directories and subdirectories
    private function pf_load_files_recursively($dir): void
    {
        // Load all PHP files in the current directory
        foreach (glob($dir . '/*.php') as $file)
        {
            require_once $file;
        }

        // Recursively load PHP files from subdirectories
        foreach (glob($dir . '/*', GLOB_ONLYDIR) as $subdir)
        {
            $this->pf_load_files_recursively($subdir);  // Calls itself for each subdirectory
        }
    }
    
    // Load plugin Routes Classes [app/Routes/RoutesBase.php]
    private function pf_load_routes_base_classes(): void
    {
        // Fires when the plugin finishes loading classes
        do_action( 'plugin_frame_load_routes_classes_start' );

        // Load classes to load framework files
        new \PluginFrame\Routes\RoutesBase();
        
        // Fires when the plugin finishes loading classes
        do_action( 'plugin_frame_load_routes_classes_end' );
    }
    
    // Load plugin Api Classes [app/Api/ApiBase.php]
    private function pf_load_api_base_classes(): void
    {
        // Fires when the plugin finishes loading classes
        do_action( 'plugin_frame_load_api_classes_start' );

        // Load classes to load framework files
        new \PluginFrame\Api\ApiBase();
        
        // Fires when the plugin finishes loading classes
        do_action( 'plugin_frame_load_api_classes_end' );
    }

    // Load plugin Providers Classes [app/Providers/LoadProviders.php]
    private function pf_load_providers_classes(): void
    {
        // Fires when the plugin finishes loading classes
        do_action( 'plugin_frame_load_providers_classes_start' );

        // Load classes to load framework files
        new \PluginFrame\LoadProviders();
        
        // Fires when the plugin finishes loading classes
        do_action( 'plugin_frame_load_providers_classes_end' );
    }
}