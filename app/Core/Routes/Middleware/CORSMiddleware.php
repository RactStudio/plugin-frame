<?php

namespace PluginFrame\Core\Routes\Middleware;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

class CORSMiddleware
{
    public function handle($request)
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Authorization, Content-Type");
        
        return true;
    }
}
