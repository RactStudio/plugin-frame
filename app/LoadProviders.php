<?php

namespace PluginFrame;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

class LoadProviders
{
    public function __construct()
    {
        // Load all providers internal classes
        new \PluginFrame\Providers\Actions();
        new \PluginFrame\Providers\Activation();
        new \PluginFrame\Providers\Deactivation();
        new \PluginFrame\Providers\EnqueueAssets();
        new \PluginFrame\Providers\Filters();
        new \PluginFrame\Providers\Menus();
        new \PluginFrame\Providers\PostTypes();
        new \PluginFrame\Providers\ShortCodes();
        new \PluginFrame\Providers\Taxonomies();
        new \PluginFrame\Providers\Widgets();
    }
}
