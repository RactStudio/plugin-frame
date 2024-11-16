<?php

namespace PluginFrame\Helpers\Admin;

use PluginFrame\Services\Views;

class Dashboard
{
    /**
     * Render the content of the dashboard page.
     *
     * @return void
     */
    public function render(): void
    {
        echo Views::render('admin/dashboard', [
            'title' => 'Dashboard',
            'content' => 'Plugin Frame Admin Dashboard!',
        ]);
    }
}
