<?php

namespace PluginFrame\Config;

use PluginFrame\Core\Services\Container;
use PluginFrame\Core\Services\Options\Interfaces\OptionStorageInterface;
use PluginFrame\Core\Services\Options\WPOptionStorage;
use PluginFrame\Core\Services\Options\CustomTableOption;
use PluginFrame\Core\Services\Options\OptionManager;

class Options
{
    public function __construct()
    {
        $this->pf_options_container_loader();
    }

    private function pf_options_container_loader(): void
    {
        Container::bind(
            OptionStorageInterface::class . ':wp',
            fn($c) => new WPOptionStorage()
        );
        Container::bind(
            OptionStorageInterface::class . ':custom',
            fn($c) => new CustomTableOption()
        );
        Container::bind(
            OptionManager::class,
            fn($c) => new OptionManager($c)
        );
        Container::bind(
            OptionStorageInterface::class,
            fn($c) => $c->get(OptionStorageInterface::class . ':wp')
        );
    }
}
