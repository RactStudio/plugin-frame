<?php
namespace PluginFrame\Core\Helpers;

use PluginFrame\Core\Services\Container;
use PluginFrame\Core\Services\Options\OptionManager;

if (! defined('ABSPATH')) {
    exit;
}

if (! function_exists('pf_options')) {
    /**
     * Shortcut to retrieve the global OptionManager.
     *
     * @return OptionManager
     */
    function pf_options(): OptionManager
    {
        // Use the static resolve() alias to fetch the manager
        return Container::resolve(OptionManager::class);
    }
}
