<?php
namespace PluginFrame\Services;

use Exception;
use WP_Screen;
use PluginFrame\Services\Views;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

class ScreenHelp
{
    protected $groups = [];
    protected $twig;
    protected $globalContext = [];
    protected $currentScreenId;

    public function __construct()
    {
        $this->twig = new Views();
        add_action('admin_head', [$this, 'registerGroups']);
    }

    /**
     * Start configuration for a specific screen
     */
    public function forScreen(string $screenId): self
    {
        $this->currentScreenId = $screenId;
        
        if (!isset($this->groups[$screenId])) {
            $this->groups[$screenId] = [
                'tabs' => [],
                'shared_context' => []
            ];
        }
        
        return $this;
    }

    /**
     * Add a help tab to current screen group
     */
    public function addTab(string $tabId, array $config): self
    {
        if (!$this->currentScreenId) {
            throw new \RuntimeException('No screen selected. Use forScreen() first.');
        }

        $this->groups[$this->currentScreenId]['tabs'][$tabId] = wp_parse_args($config, [
            'template' => "screen-help/{$this->currentScreenId}/{$tabId}.twig",
            'title' => __('Help Documentation', 'plugin-frame'),
            'context' => [],
            'priority' => 10,
            'capability' => 'manage_options'
        ]);

        return $this;
    }

    /**
     * Add shared context for current screen group
     */
    public function withSharedContext(array $context): self
    {
        if (!$this->currentScreenId) {
            throw new \RuntimeException('No screen selected. Use forScreen() first.');
        }

        $this->groups[$this->currentScreenId]['shared_context'] = array_merge(
            $this->groups[$this->currentScreenId]['shared_context'],
            $context
        );

        return $this;
    }

    /**
     * Register all tab groups with WordPress
     */
    public function registerGroups()
    {
        $screen = get_current_screen();
        if (!$screen || !isset($this->groups[$screen->id])) return;

        foreach ($this->groups[$screen->id]['tabs'] as $tabId => $tabConfig) {
            $this->registerSingleTab($screen, $tabId, $tabConfig);
        }
    }

    protected function registerSingleTab(WP_Screen $screen, string $tabId, array $tabConfig)
    {
        if (!current_user_can($tabConfig['capability'])) return;

        $screen->add_help_tab([
            'id' => $tabId,
            'title' => $tabConfig['title'],
            'content' => $this->renderTabContent($tabConfig),
            'priority' => $tabConfig['priority']
        ]);
    }

    protected function renderTabContent(array $tabConfig): string
    {
        try {
            // Fix parameter order and template extension handling
            return Views::render(
                $tabConfig['template'],  // Template path without extension
                'twig',                  // Explicitly set extension
                $this->mergeContexts($tabConfig)
            );
        } catch (Exception $e) {
            error_log("Help Tab Error [{$tabConfig['template']}]: {$e->getMessage()}");
            return __('Error loading help content', 'plugin-frame');
        }
    }

    protected function mergeContexts(array $tabConfig): array
    {
        return array_merge(
            $this->globalContext,
            $this->groups[$this->currentScreenId]['shared_context'],
            $tabConfig['context']
        );
    }

    /**
     * Add global context available to all tabs
     */
    public function addGlobalContext(array $context): self
    {
        $this->globalContext = array_merge($this->globalContext, $context);
        return $this;
    }

    public function adjustAdminStyles()
    {
        add_action('admin_head', function() {
            $screen = get_current_screen();
            $margin_needed = isset($this->groups[$screen->id]) || $screen->show_screen_options();
    
            if ($margin_needed) {
                echo '<style>
                    #pf-load.pf-page  {
                        margin-top: 30px;
                        position: relative;
                        z-index: 999;
                    }
                    @media screen and (max-width: 782px) {
                        #pf-load.pf-page {
                            margin-top: 40px;
                        }
                    }
                    #screen-meta-links {
                        margin-bottom: 0px;
                    }
                </style>';
            }
        });
    }

}