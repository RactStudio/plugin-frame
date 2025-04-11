<?php

namespace PluginFrame\Core\Updater;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

use PluginFrame\Config\Updater;

class CustomUpdater {
    public function __construct() {
        add_filter('site_transient_update_plugins', [$this, 'checkUpdates']);
        add_filter('plugins_api', [$this, 'getPluginInfo'], 10, 3);
    }

    public function checkUpdates($transient) {
        $response = wp_remote_get(Updater::CUSTOM['endpoint'], [
            'headers' => ['X-API-Key' => Updater::CUSTOM['key']]
        ]);
        
        if (200 === wp_remote_retrieve_response_code($response)) {
            $data = json_decode(wp_remote_retrieve_body($response));
            
            if ($this->validateResponse($data)) {
                $transient->response[PLUGIN_FRAME_BASENAME] = (object)[
                    'slug'        => PLUGIN_FRAME_SLUG,
                    'plugin'      => PLUGIN_FRAME_BASENAME,
                    'new_version' => $data->version,
                    'package'     => $data->download_url,
                    'signature'   => $data->signature
                ];
            }
        }
        return $transient;
    }
    
    private function validateResponse($data) {
        if (Updater::VERIFICATION['check_signature']) {
            return openssl_verify(
                $data->version . $data->download_url,
                base64_decode($data->signature),
                Updater::VERIFICATION['public_key']
            ) === 1;
        }
        return true;
    }

    public function getPluginInfo($false, $action, $args) {
        if ($args->slug !== PLUGIN_FRAME_SLUG) return $false;
    
        $response = wp_remote_get(Updater::CUSTOM['endpoint'], [
            'headers' => ['X-API-Key' => Updater::CUSTOM['key']]
        ]);
    
        if (200 !== wp_remote_retrieve_response_code($response)) {
            return $false;
        }
    
        $data = json_decode(wp_remote_retrieve_body($response));
    
        return (object)[
            'name' => PLUGIN_FRAME_NAME,
            'slug' => PLUGIN_FRAME_SLUG,
            'version' => $data->version,
            'download_link' => $data->download_url,
            'sections' => [
                'changelog' => $data->changelog ?? ''
            ],
            'tested' => $data->tested ?? PLUGIN_FRAME_MAX_WP,
            'requires_php' => $data->requires_php ?? PLUGIN_FRAME_MIN_PHP
        ];
    }
}