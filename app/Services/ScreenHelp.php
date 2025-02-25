<?php
namespace PluginFrame\Services;

use Exception;
use RuntimeException;
use WP_Screen;
use PluginFrame\Services\Views;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Manages WordPress admin help tabs with Twig template support
 * 
 * Provides fluent interface for registering context-aware help tabs
 * Handles template rendering, context merging, and access control
 */
class ScreenHelp
{
    protected $groups = [];
    protected $twig;
    protected $globalContext = [];
    protected $currentScreenId;

    /**
     * Initialize service and register WordPress hooks
     */
    public function __construct()
    {
        $this->twig = new Views();
        add_action('admin_head', [$this, 'registerGroups']);
    }

    /**
     * Set current screen for help tab configuration
     * 
     * @param string $screenId WordPress screen ID (e.g., 'dashboard', 'edit-post')
     * @return self Fluent interface
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
     * Register a help tab for current screen
     * 
     * @param string $tabId Unique tab identifier
     * @param array $config {
     *     @type string   $template    Relative path to Twig template (without extension)
     *     @type string   $title       Display title
     *     @type array    $context     Tab-specific template variables
     *     @type int      $priority    Display order (lower numbers first)
     *     @type string   $capability  Required user capability
     * }
     * @return self Fluent interface
     * @throws RuntimeException If no screen selected
     */
    public function addTab(string $tabId, array $config): self
    {
        if (!$this->currentScreenId) {
            throw new RuntimeException('No screen selected. Use forScreen() first.');
        }

        $this->groups[$this->currentScreenId]['tabs'][$tabId] = wp_parse_args($config, [
            'template' => "screen-help/{$this->currentScreenId}/{$tabId}.twig",
            'title' => _x('Help Documentation', PLUGIN_FRAME_SLUG),
            'context' => [],
            'priority' => 10,
            'capability' => 'manage_options'
        ]);

        return $this;
    }

    /**
     * Add shared template variables for current screen's tabs
     * 
     * @param array $context Associative array of template variables
     * @return self Fluent interface
     * @throws RuntimeException If no screen selected
     */
    public function withSharedContext(array $context): self
    {
        if (!$this->currentScreenId) {
            throw new RuntimeException('No screen selected. Use forScreen() first.');
        }

        $this->groups[$this->currentScreenId]['shared_context'] = array_merge(
            $this->groups[$this->currentScreenId]['shared_context'],
            $context
        );

        return $this;
    }

    /**
     * Register all configured help tabs with WordPress
     * 
     * Automatically called via admin_head hook
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
            'title' => _x($tabConfig['title'], PLUGIN_FRAME_SLUG),
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
        // Get current screen from WordPress
        $screen = get_current_screen();
        
        // Verify we have valid screen and context data
        if (!$screen || !isset($this->groups[$screen->id])) {
            return array_merge(
                $this->globalContext,
                $tabConfig['context']
            );
        }
    
        // Use screen ID from current context
        return array_merge(
            $this->globalContext,
            $this->groups[$screen->id]['shared_context'],
            $tabConfig['context']
        );
    }

    /**
     * Add global template variables available to all screens
     * 
     * @param array $context Associative array of template variables
     * @return self Fluent interface
     */
    public function addGlobalContext(array $context): self
    {
        $this->globalContext = array_merge($this->globalContext, $context);
        return $this;
    }

    /**
     * Adjust admin page styles when help tabs are present
     * 
     * Adds dynamic CSS for proper spacing above Plugin Frame Page Content
     * Applies responsive margins for mobile devices
     */
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
                    div#screen-meta-links div#contextual-help-link-wrap,
                    div#screen-meta, div#screen-meta div#contextual-help-wrap {
                        z-index: 9999;
                    }
                </style>';
            }
        });
    }

}