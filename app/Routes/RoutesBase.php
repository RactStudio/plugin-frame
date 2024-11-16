<?php

namespace PluginFrame\Routes;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

class RoutesBase
{
    // Define your routes here
    public function __construct()
    {
        // Register your routes here
        //add_action( 'rest_api_init', [$this, 'register_routes'] );
        echo '<h2>--------------- Routes:</h2>';
    }
}