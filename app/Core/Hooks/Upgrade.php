<?php

namespace PluginFrame\Core\Hooks;

// Exit if accessed directly
if (!defined('ABSPATH')) { exit; }

class Upgrade
{
    public function __construct()
    {
        $this->registerHooks();
    }

    protected function registerHooks(): void
    {
        add_action('upgrader_process_complete', [$this, 'onUpgradeProcessComplete'], 10, 2);
        add_filter('upgrader_pre_install', [$this, 'beforeUpload'], 10, 2);
        add_filter('upgrader_post_install', [$this, 'afterUpload'], 10, 2);
    }

    public function onUpgradeProcessComplete($upgrader, $options): void
    {
        if ($this->isPluginUpdate($options)) {
            $this->handleUpgradeProcess();
            do_action('plugin_frame_on_plugin_process_complete', $upgrader, $options);
        }
    }

    protected function isPluginUpdate(array $options): bool
    {
        return isset($options['type'], $options['plugins']) 
            && $options['type'] === 'plugin'
            && in_array(PLUGIN_FRAME_BASENAME, $options['plugins'], true);
    }

    protected function handleUpgradeProcess(): void
    {
        error_log(PLUGIN_FRAME_NAME . ' upgrade process started.');
        $this->performUpgradeTasks();
        error_log(PLUGIN_FRAME_NAME . ' upgraded successfully.');
    }

    protected function performUpgradeTasks(): void
    {
        $currentVersion = get_option('plugin_frame_version', '1.0.0');
        
        if (version_compare($currentVersion, PLUGIN_FRAME_VERSION, '<')) {
            $this->updateDatabaseSchema();
            update_option('plugin_frame_version', PLUGIN_FRAME_VERSION);
        }
    }

    protected function updateDatabaseSchema(): void
    {
        // Core schema updates (override in child)
    }

    public function beforeUpload($return, $hookExtra)
    {
        if ($this->isPluginUpload($hookExtra)) {
            $this->handleBeforeUpload($hookExtra);
        }
        return $return;
    }

    public function afterUpload($response, $hookExtra)
    {
        if ($this->isPluginUpload($hookExtra)) {
            $this->handleAfterUpload($response, $hookExtra);
        }
        return $response;
    }

    protected function isPluginUpload(array $hookExtra): bool
    {
        return isset($hookExtra['type']) && $hookExtra['type'] === 'plugin';
    }

    protected function handleBeforeUpload(array $hookExtra): void
    {
        error_log('Before Upload Hook Triggered.');
        do_action('plugin_frame_before_plugin_upload', $hookExtra);
    }

    protected function handleAfterUpload($response, array $hookExtra): void
    {
        error_log('After Upload Hook Triggered.');
        do_action('plugin_frame_after_plugin_upload', $response, $hookExtra);
    }
}