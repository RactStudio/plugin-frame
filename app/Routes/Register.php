<?php

namespace PluginFrame\Routes;

// Exit if accessed directly
if (!defined('ABSPATH')) { exit; }

use PluginFrame\Config\Routes;

class Register
{
    /**
     * Initialize route registration
     */
    public function __construct()
    {
        add_action('rest_api_init', [$this, 'registerAllRoutes']);
    }

    /**
     * Dynamically include and register routes
     */
    public function registerAllRoutes()
    {
        //Register Routes
        new Routes();
    }

}
