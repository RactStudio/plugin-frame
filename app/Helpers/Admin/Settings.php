<?php

namespace PluginFrame\Helpers\Admin;

use PluginFrame\Services\Views;

class Settings
{
    /**
     * Render the settings page content.
     *
     * @return void
     */
    public function render(): void
    {
        echo Views::render('admin/settings', [
            'title' => 'Settings',
            'content' => 'Plugin Frame Settings',
        ]);
    }
}
