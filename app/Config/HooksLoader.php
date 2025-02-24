<?php

namespace PluginFrame\Config;

use PluginFrame\Helpers\LanguageTextDomain;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

class HooksLoader
{
    // Define your API routes here
    public function __construct()
    {
        // Load languages
        add_action('init', [new LanguageTextDomain(), 'load_textdomain'], 9999);


    }
}