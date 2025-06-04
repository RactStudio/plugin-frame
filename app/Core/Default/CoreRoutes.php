<?php

namespace PluginFrame\Core\Default;

class CoreRoutes
{
    /**
     * DefaultRoutes constructor.
     */
    public function __construct()
    {
        // Load the default core routes
        $this->load_default_routes();
    }

    /**
     * Load the default routes.
     */
    private function load_default_routes(): void
    {
        // Load the core components
        new \PluginFrame\Config\Options();
        new \PluginFrame\Config\HooksLoader();
        new \PluginFrame\Config\Routes();
    }
}