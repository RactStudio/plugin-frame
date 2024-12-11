<?php

namespace PluginFrame\Config;

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
    public function load_with_php_version_check()
    {
        // Check minimum PHP version required for the plugin.
        if (version_compare(PHP_VERSION, PLUGIN_FRAME_MIN_PHP, '<')) {
            add_action('admin_notices', function ()
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

        // Load framework files to load classes
        $this->load_directories_files();

        // Load plugin default features priority first
        $this->load_plugin_frame_config('first');

        // Load plugin frame Classes
        $this->load_plugin_frame_classes();

        // Load plugin Routes Classes
        $this->load_routes_base_classes();

        // Load plugin Api Classes
        $this->load_api_base_classes();

        // Load plugin default features priority last
        $this->load_plugin_frame_config('last');

        // Load plugin frame debugeer
        $this->load_plugin_frame_debug();

        // Fires when the plugin finishes loading completely
        do_action( 'plugin_frame_load_end' );

    }

    // Load plugin default features priority first or last
    private function load_plugin_frame_config($priority): void
    {
        // Fires when the config started loading
        do_action( 'plugin_frame_config_load_start' );

        if ($priority === 'first') {
            // Load plugin default features priority first
            (new \PluginFrame\Config\Config())->priority_load_first();
        } elseif ($priority === 'last') {
            // Load plugin default features priority last
            (new \PluginFrame\Config\Config())->priority_load_last();
        }

        // Fires when the config finishes loading
        do_action( 'plugin_frame_config_load_end' );

    }

    // Load composer vendor files
    private function load_composer_vendor(): void
    {
        // Fires when the composer started loading
        do_action( 'plugin_frame_composer_load_start' );

        // Autoload Composer dependencies
        if ( file_exists( PLUGIN_FRAME_DIR . 'vendor/autoload.php' ) )
        {
            require_once PLUGIN_FRAME_DIR . 'vendor/autoload.php';
        }
        
        // Fires when the composer finishes loading
        do_action( 'plugin_frame_composer_load_end' );
    }

    // Load PHP files from directories and subdirectories
    private function load_directories_files(): void
    {
        // Fires when the directories and subdirectories started loading
        do_action( 'plugin_frame_directories_load_start' );

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

        // Fires when the directories and subdirectories finishes loading
        do_action( 'plugin_frame_directories_load_end' );
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
        do_action( 'plugin_frame_routes_classes_load_start' );

        // Load routes classes to register WordPress REST API
        //new \PluginFrame\Config\RoutesTest();


        // Hook into WordPress REST API initialization
        \add_action(
            'rest_api_init',
            function ()
            {
                // Register a single route
                register_rest_route('plugin-frame/v1', '/test', [
                    'methods' => 'GET',
                    'callback' => function ($request) {
                        return rest_ensure_response(['message' => 'This is the test endpoint response.']);
                    },
                    'permission_callback' => '__return_true',
                ]);

                // Middleware for group routes
                $middleware = function ($request) {
                    // Example middleware logic: allow all requests
                    return true;
                };

                // Group routes with middleware
                $groupMiddleware = function () use ($middleware) {
                    // Register a route within the group
                    register_rest_route('plugin-frame/v1', '/public', [
                        'methods' => 'GET',
                        'callback' => function ($request) {
                            return rest_ensure_response(['message' => 'This is the public endpoint response.']);
                        },
                        'permission_callback' => $middleware, // Apply middleware here
                    ]);
                };

                // Call the grouped routes
                $groupMiddleware();

        });


        
        // Fires when the plugin finishes loading classes
        do_action( 'plugin_frame_routes_classes_load_end' );
    }
    
    // Load plugin Api Classes [app/Api/ApiBase.php]
    private function load_api_base_classes(): void
    {
        // Fires when the plugin finishes loading classes
        do_action( 'plugin_frame_api_classes_load_start' );

        // Load classes to load framework files
        new \PluginFrame\Api\ApiBase();
        
        // Fires when the plugin finishes loading classes
        do_action( 'plugin_frame_api_classes_load_end' );
    }

    // Load plugin frame Classes
    private function load_plugin_frame_classes(): void
    {
        // Fires when the plugin finishes loading classes
        do_action( 'plugin_frame_providers_classes_load_start' );

        // Load classes to load framework files
        new \PluginFrame\Config\Providers();
        
        // Fires when the plugin finishes loading classes
        do_action( 'plugin_frame_providers_classes_load_end' );
    }

    // Load plugin debugeer files
    public function load_plugin_frame_debug(): void
    {
        if ( file_exists( PLUGIN_FRAME_DIR . 'app/Debug/Helpers.php' ) )
        {
            // Fires when the debugger started loading
            do_action( 'plugin_frame_debugger_load_start' );

            // Load Debugger helper functions file
            require_once PLUGIN_FRAME_DIR . 'app/Debug/Helpers.php';

            // Fires when the debugger finishes loading
            do_action( 'plugin_frame_debugger_load_end' );
        }
    }

}