<?php 

namespace PluginFrame\Providers;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

class PostTypes
{
    // Add Custom Post Types
    public function __construct()
    {
        echo '<h2>--------------- PostTypes:</h2>';
    }
}