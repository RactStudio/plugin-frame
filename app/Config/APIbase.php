<?php

namespace PluginFrame\Config;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

class APIbase
{
    // Define your API routes here
    public function url(): string
    {
        // Provide API base URL here
        //return 'https://api.example.com';
        return 'http://127.0.0.1:80';
    }
}