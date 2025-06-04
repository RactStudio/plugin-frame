<?php

namespace PluginFrame\Config;

use PluginFrame\Core\Helpers\ProvidersHelper;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Providers
{
    /**
     * Lists of classes to load before and after automatic scanning.
     *
     * @var array{priority_first: string[], priority_last: string[]}
     */
    protected array $classes = [
        'priority_first' => [
            // 'PluginFrame\Providers\SomeEarlyProvider',
        ],
        'priority_last' => [
            // 'PluginFrame\Providers\SomeLateProvider',
        ],
    ];

    protected ProvidersHelper $providersHelper;
    protected string $directory     = PLUGIN_FRAME_DIR . 'app/Providers/';
    protected string $baseNamespace = 'PluginFrame\Providers';

    public function __construct()
    {
        $this->providersHelper = new ProvidersHelper();

        // 1) Load priority-first providers
        $this->providersHelper->loadPriorityClasses($this->classes, 'priority_first');

        // 3) Load all view classes under app/Views/
        $this->providersHelper->loadViewClasses();

        // 2) Autoload all other providers
        $this->providersHelper->loadProvidersClasses($this->classes, $this->directory, $this->baseNamespace);

        // 4) Load priority-last providers
        $this->providersHelper->loadPriorityClasses($this->classes, 'priority_last');
    }
}
