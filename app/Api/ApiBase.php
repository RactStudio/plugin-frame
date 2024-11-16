<?php

namespace PluginFrame\Api;

class ApiBase
{
    // Define your API routes here
    public function __construct()
    {
        // Register your routes here
        //add_action( 'rest_api_init', [$this, 'register_routes'] );
        echo '<h2>--------------- Api:</h2>';
    }
}