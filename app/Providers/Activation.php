<?php 

namespace PluginFrame\Providers;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Activation
{
    // Plugin Activation
    public function __construct()
    {
        echo '<h2>--------------- Activation:</h2>';
    }
}