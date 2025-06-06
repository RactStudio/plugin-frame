<?php

namespace PluginFrame\Config;

use PluginFrame\Core\Helpers\LanguageTextDomain;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

class HooksLoader
{
    public function __construct()
    {
        // Load languages
        add_action('init', [new LanguageTextDomain(), 'load_textdomain'], 9999);


    }
}