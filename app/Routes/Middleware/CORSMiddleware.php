<?php

namespace PluginFrame\Routes\Middleware;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

class CORSMiddleware
{
    public function handle($request)
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        return true;
    }
}
