<?php

namespace PluginFrame\Core\Updater;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

class CoreUpdater {
    const CORE_API = 'https://core.plugin-frame.com/v1/check-update';
    
    // Core update paths (relative to plugin dir)
    const CORE_PATHS = [
        'app/Core/'
    ];
    
    public function __construct() {
        add_filter('site_transient_update_plugins', [$this, 'checkUpdates']);
        add_filter('upgrader_post_install', [$this, 'installCoreUpdate'], 10, 3);
    }

    public function checkUpdates($transient) {
        $response = wp_remote_get(add_query_arg([
            'version' => $this->currentVersion(),
            'php'     => phpversion(),
            'wp'      => get_bloginfo('version')
        ], self::CORE_API));
        
        if (200 === wp_remote_retrieve_response_code($response)) {
            $data = json_decode(wp_remote_retrieve_body($response));
            
            if ($data->version !== $this->currentVersion()) {
                $transient->response['plugin-frame/app/Core'] = (object)[
                    'slug'        => 'plugin-frame-core',
                    'plugin'      => PLUGIN_FRAME_BASENAME,
                    'new_version' => $data->version,
                    'package'     => $data->download_url,
                    'paths'       => self::CORE_PATHS,
                ];
            }
        }
        return $transient;
    }

    public function installCoreUpdate($response, $hook_extra, $result) {
        if (isset($hook_extra['core_update'])) {
            $this->moveFiles(
                $result['local_destination'],
                PLUGIN_FRAME_DIR,
                self::CORE_PATHS
            );
        }
        return $result;
    }
    
    private function currentVersion() {
        return get_file_data(PLUGIN_FRAME_FILE, ['Version'])[0];
    }
    
    private function moveFiles($source, $dest, $paths) {
        foreach ($paths as $path) {
            $this->recurseCopy("$source/$path", "$dest/$path");
        }
    }

    private function recurseCopy($src, $dst) {
        $dir = opendir($src);
        @mkdir($dst, 0755, true);
        
        while (($file = readdir($dir)) !== false) {
            if ($file != '.' && $file != '..') {
                $srcFile = $src . '/' . $file;
                $dstFile = $dst . '/' . $file;
                
                if (is_dir($srcFile)) {
                    $this->recurseCopy($srcFile, $dstFile);
                } else {
                    copy($srcFile, $dstFile);
                }
            }
        }
        closedir($dir);
    }
}