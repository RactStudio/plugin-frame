<?php
namespace PluginFrame\Core\Updater;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

use RuntimeException;
use PluginFrame\Config\Updater;

class UpdateManager {
    private $active_source;
    
    public function __construct() {
        $this->validateSources();
        $this->initUpdaters();
        $this->filterWpUpdates();
    }

    private function validateSources() {
        $external_sources = array_filter([
            'github' => Updater::SOURCES['github'],
            'custom' => Updater::SOURCES['custom']
        ]);
        
        if (count($external_sources) > 1) {
            throw new RuntimeException('Only one external update source can be active');
        }
        
        $this->active_source = key($external_sources) ?? 'wp_org';
    }

    private function initUpdaters() {
        // Always initialize core updater
        new CoreUpdater();

        switch ($this->active_source) {
            case 'github':
                new GitHubUpdater();
                break;
            case 'custom':
                new CustomUpdater();
                break;
        }
    }

    private function filterWpUpdates() {
        add_filter('http_request_args', function($args, $url) {
            if ($this->active_source !== 'wp_org' && 
                strpos($url, 'api.wordpress.org/plugins/update-check') !== false) {
                $args['body']['plugins'][PLUGIN_FRAME_BASENAME] = [
                    'Version' => '999.0.0', // Spoof version
                    'update' => false
                ];
            }
            return $args;
        }, 10, 2);
    }

    private function cleanupSources() {
        add_filter('pre_set_site_transient_update_plugins', function($transient) {
            if ($this->active_source !== 'wp_org' && isset($transient->response[PLUGIN_FRAME_BASENAME])) {
                unset($transient->response[PLUGIN_FRAME_BASENAME]);
            }
            return $transient;
        });
    }
}