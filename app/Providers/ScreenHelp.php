<?php
namespace PluginFrame\Providers;

use PluginFrame\Services\ScreenHelp as ScreenHelpService;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Provider class for registering help tabs
 * 
 * Example implementation demonstrating help tab configuration
 * Defines help content structure for specific admin pages
 */
class ScreenHelp
{
    protected $screenHelp;

    /**
     * Initialize service and register help tabs
     */
    public function __construct()
    {
        $this->screenHelp = new ScreenHelpService();
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
                'plugin_domain'    => PLUGIN_FRAME_SLUG,
                'plugin_frame_name'=> PLUGIN_FRAME_NAME,
                'plugin_frame_url' => PLUGIN_FRAME_URL,
                'support_email' => 'support@pluginframe.com',
                'docs_url' => 'https://docs.pluginframe.com',
            ])
            ->addTab('getting-started', [
                'title' => _x('Getting Started', PLUGIN_FRAME_SLUG),
                'template' => 'screen-help/dashboard/getting-started', // Remove .twig extension
                'priority' => 10,
                'capability' => 'manage_options'
            ])
            ->addTab('advanced-usage', [
                'title' => _x('Advanced Usage', PLUGIN_FRAME_SLUG),
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
            ->forScreen('plugin-frame_page_plugin-frame-settings')
            ->withSharedContext([
                'plugin_domain'    => PLUGIN_FRAME_SLUG,
                'plugin_frame_name'=> PLUGIN_FRAME_NAME,
                'plugin_frame_url' => PLUGIN_FRAME_URL,
                'privacy_policy_url' => '#privacy'
            ])
            ->addTab('general-settings', [
                'title' => _x('General Settings', PLUGIN_FRAME_SLUG),
                'template' => 'screen-help/settings/general-settings' // Remove .twig extension
            ])
            ->addTab('privacy', [
                'title' => _x('Privacy Guide', PLUGIN_FRAME_SLUG),
                'template' => 'screen-help/settings/privacy', // Remove .twig extension
                'capability' => 'manage_privacy_options'
            ]);
    }

}