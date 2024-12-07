<?php

namespace PluginFrame\Routes;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

use PluginFrame\Routes\Routes;

class RoutesRegister
{
    /**
     * Initialize route registration
     */
    public function __construct()
    {
        // Hook into WordPress REST API initialization
        add_action( 'rest_api_init', [ $this, 'registerAllRoutes' ] );
    }

    /**
     * Dynamically include routes and register them
     */
    public function registerAllRoutes()
    {
        // Create an instance of the Routes class
        $routes = new Routes();

        // Call the method to register all routes
        $routes->registerRoutes();
    }
}
