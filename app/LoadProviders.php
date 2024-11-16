<?php

namespace PluginFrame;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

class LoadProviders
{
    public function __construct()
    {
        // Load all providers internal classes
        new \PluginFrame\Providers\EnqueueAssets();
        new \PluginFrame\Providers\Activation();
        new \PluginFrame\Providers\Deactivation();
        new \PluginFrame\Providers\Customize();
        new \PluginFrame\Providers\Menus();
        new \PluginFrame\Providers\Actions();
        new \PluginFrame\Providers\Filters();
        new \PluginFrame\Providers\PostTypes();
        new \PluginFrame\Providers\Taxonomies();
        new \PluginFrame\Providers\Widgets();
        new \PluginFrame\Providers\ShortCodes();
    }
}
