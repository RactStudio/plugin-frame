<?php

namespace PluginFrame\Routes\Middleware;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

class PublicMiddleware
{
    /**
     * Handle logic for public routes (if needed)
     */
    public function handle($request)
    {
        // Add any preprocessing logic for public routes
        return true;
    }
}
