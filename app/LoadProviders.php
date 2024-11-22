<?php

namespace PluginFrame;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

class LoadProviders
{
    public function __construct()
    {

        /**
         * Few classes need to be loaded fast to execute first to make sure it purpose is achived
         * Eg: Init.php need to be initialized first
         */

        // Load / Execute - all providers internal classes
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
