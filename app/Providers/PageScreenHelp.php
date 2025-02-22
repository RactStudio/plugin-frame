<?php
namespace PluginFrame\Providers;

use PluginFrame\Services\ScreenHelp;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Provider class for registering help tabs
 * 
 * Example implementation demonstrating help tab configuration
 * Defines help content structure for specific admin pages
 */
class PageScreenHelp
{
    protected $screenHelp;

    /**
     * Initialize service and register help tabs
     */
    public function __construct()
    {
        $this->screenHelp = new ScreenHelp();
        $this->screenHelp->adjustAdminStyles();
        $this->registerHelpTabs();
    }

    protected function registerHelpTabs()
    {
        $this->registerMainPageHelp();
        $this->registerSettingsPageHelp();
    }

    /**
     * Register help tabs for main plugin page
     * 
     * Tabs:
     * - Getting Started: Basic usage guide
     * - Advanced Usage: Developer-focused documentation
     * 
     * Shared context available to both tabs:
     * - support_email: Support contact address
     * - docs_url: Documentation portal URL
     */
    protected function registerMainPageHelp()
    {
        $this->screenHelp
            ->forScreen('toplevel_page_plugin-frame')
            ->withSharedContext([
                'support_email' => 'support@pluginframe.com',
                'docs_url' => 'https://docs.pluginframe.com',
                'hello_world' => 'Hello World'
            ])
            ->addTab('getting-started', [
                'title' => __('Getting Started', 'plugin-frame'),
                'template' => 'screen-help/dashboard/getting-started', // Remove .twig extension
                'priority' => 10,
                'capability' => 'manage_options'
            ])
            ->addTab('advanced-usage', [
                'title' => __('Advanced Usage', 'plugin-frame'),
                'template' => 'screen-help/dashboard/advanced-usage', // Remove .twig extension
                'priority' => 20
            ]);
    }
    
    /**
     * Register help tabs for settings page
     * 
     * Tabs:
     * - General Settings: Configuration guidelines
     * - Privacy Guide: Data handling documentation
     * 
     * Shared context:
     * - privacy_policy_url: Link to privacy policy
     */
    protected function registerSettingsPageHelp()
    {
        $this->screenHelp
            ->forScreen('plugin-frame_page_pf-settings')
            ->withSharedContext([
                'privacy_policy_url' => '#privacy'
            ])
            ->addTab('general-settings', [
                'title' => __('General Settings', 'plugin-frame'),
                'template' => 'screen-help/settings/general-settings' // Remove .twig extension
            ])
            ->addTab('privacy', [
                'title' => __('Privacy Guide', 'plugin-frame'),
                'template' => 'screen-help/settings/privacy', // Remove .twig extension
                'capability' => 'manage_privacy_options'
            ]);
    }

}