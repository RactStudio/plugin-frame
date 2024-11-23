<?php

namespace PluginFrame\Providers;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Init
{
    // Add WP primary init actions and filters,
    // including conditions based on admin / front-end / etc
    public function __construct()
    {
        // Do something
    }

}