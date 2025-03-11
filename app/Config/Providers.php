<?php

namespace PluginFrame\Config;

use PluginFrame\Helpers\ProvidersHelper;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Providers
{
    /**
     * Add each class manually.
     * Only from directory inside [app/Providers]
     * These will be loaded first and last,
     * and will exclude from the standard providers classes loading.
     */
    protected $classes = [
        'priority_first' => [
            // Classes to load first
            // Add classes here
        ],
        'priority_last' => [
            // Classes to load last
            // Add classes here
        ],
    ];
    protected $providersHelper;
    protected $directory = PLUGIN_FRAME_DIR . 'app/Providers/';
    protected $baseNamespace = 'PluginFrame\Providers';
    
    /**
     * Constructor.
     * Loads priority classes first, then dynamically loads standard providers,
     * and finally loads priority last classes.
     */
    public function __construct()
    {
        $this->providersHelper = new ProvidersHelper();

        $this->providersHelper->loadPriorityClasses($this->classes, 'priority_first');
        $this->providersHelper->loadProvidersClasses($this->classes, $this->directory, $this->baseNamespace);
        $this->providersHelper->loadPriorityClasses($this->classes, 'priority_last');
    }
}