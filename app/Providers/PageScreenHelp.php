<?php
namespace PluginFrame\Providers;

use PluginFrame\Services\ScreenHelp;

class PageScreenHelp
{
    protected $screenHelp;

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

    protected function registerMainPageHelp()
    {
        $this->screenHelp
            ->forScreen('toplevel_page_plugin-frame')
            ->withSharedContext([
                'support_email' => 'support@pluginframe.com',
                'docs_url' => 'https://docs.pluginframe.com'
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