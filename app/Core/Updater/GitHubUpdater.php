<?php

namespace PluginFrame\Core\Updater;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

use PluginFrame\Config\Updater;

class GitHubUpdater {
    const API_URL = 'https://api.github.com/repos/%s/%s/releases/latest';
    
    public function __construct() {
        add_filter('site_transient_update_plugins', [$this, 'checkUpdates']);
        add_filter('plugins_api', [$this, 'getPluginInfo'], 10, 3);
    }

    public function checkUpdates($transient) {
        $release = $this->getReleaseData();
        
        if (version_compare($this->currentVersion(), $release->version, '<')) {
            $transient->response[PLUGIN_FRAME_BASENAME] = (object)[
                'slug'        => PLUGIN_FRAME_SLUG,
                'plugin'      => PLUGIN_FRAME_BASENAME,
                'new_version' => $release->version,
                'package'     => $this->getDownloadUrl($release),
                'tested'      => $release->tested,
                'requires_php'=> $release->requires_php
            ];
        }
        return $transient;
    }

    public function getPluginInfo($false, $action, $args) {
        if ($args->slug !== PLUGIN_FRAME_SLUG) return $false;
        
        $release = $this->getReleaseData();
        $readme  = $this->parseReadme();
        
        return (object)[
            'name'          => PLUGIN_FRAME_NAME,
            'version'       => $release->version,
            'download_link' => $this->getDownloadUrl($release),
            'sections'      => [
                'description' => $readme['description'],
                'changelog'   => $release->body
            ],
            'tested'        => $release->tested,
            'requires_php'  => $release->requires_php
        ];
    }
    
    private function getReleaseData(): array
    {
        $cache_key = 'pf_github_release_data';
        $data = get_transient($cache_key);
    
        if (false === $data) {
            $url = sprintf(
                self::API_URL,
                rawurlencode(Updater::GITHUB['owner']),
                rawurlencode(Updater::GITHUB['repo'])
            );
    
            $args = [
                'headers' => [
                    'Accept' => 'application/vnd.github.v3+json',
                    'Authorization' => Updater::GITHUB['token'] 
                        ? 'token ' . Updater::GITHUB['token'] 
                        : ''
                ]
            ];
    
            $response = wp_remote_get($url, $args);
            
            if (is_wp_error($response) || 
                wp_remote_retrieve_response_code($response) !== 200) {
                return [];
            }
    
            $body = json_decode(wp_remote_retrieve_body($response), true);
            $data = $this->parseReleaseData($body);
            set_transient($cache_key, $data, 6 * HOUR_IN_SECONDS);
        }
    
        return $data;
    }
    
    private function parseReleaseData(array $release): array
    {
        return [
            'version' => ltrim($release['tag_name'] ?? '0.0.0', 'v'),
            'download_url' => $release['assets'][0]['browser_download_url'] ?? '',
            'changelog' => $release['body'] ?? '',
            'published_at' => $release['published_at'] ?? '',
            'tested' => $this->extractMetadata(
                $release['body'] ?? '', 
                'Tested up to:', 
                PLUGIN_FRAME_MAX_WP
            ),
            'requires_php' => $this->extractMetadata(
                $release['body'] ?? '', 
                'Requires PHP:', 
                PLUGIN_FRAME_MIN_PHP
            )
        ];
    }
    
    private function parseReadme(): array
    {
        $cache_key = 'pf_github_readme_data';
        $data = get_transient($cache_key);
    
        if (false === $data) {
            $readme_url = sprintf(
                'https://raw.githubusercontent.com/%s/%s/%s/%s',
                rawurlencode(Updater::GITHUB['owner']),
                rawurlencode(Updater::GITHUB['repo']),
                rawurlencode(Updater::GITHUB['branch']),
                ltrim(Updater::GITHUB['readme_path'], '/')
            );
    
            $response = wp_remote_get($readme_url);
            
            if (is_wp_error($response) || 
                wp_remote_retrieve_response_code($response) !== 200) {
                return [];
            }
    
            $content = wp_remote_retrieve_body($response);
            $data = $this->parseReadmeContent($content);
            set_transient($cache_key, $data, 12 * HOUR_IN_SECONDS);
        }
    
        return $data;
    }
    
    private function parseReadmeContent(string $content): array
    {
        // Parse sections
        $sections = preg_split('/^===\s*(.+?)\s*===/m', $content, -1, PREG_SPLIT_DELIM_CAPTURE);
        $parsed = [];
        
        for ($i = 1; $i < count($sections); $i += 2) {
            $title = strtolower(trim($sections[$i]));
            $body = trim($sections[$i + 1] ?? '');
            $parsed[$title] = $body;
        }
    
        // Extract metadata
        return [
            'description' => $parsed['description'] ?? '',
            'changelog' => $parsed['changelog'] ?? '',
            'tested' => $this->extractMetadata($content, 'Tested up to:', PLUGIN_FRAME_MAX_WP),
            'requires_php' => $this->extractMetadata($content, 'Requires PHP:', PLUGIN_FRAME_MIN_PHP)
        ];
    }
    
    private function extractMetadata(string $content, string $field, string $fallback): string
    {
        $pattern = '/'.preg_quote($field, '/').'\s+([\d\.]+)/i';
        preg_match($pattern, $content, $matches);
        return isset($matches[1]) ? sanitize_text_field($matches[1]) : $fallback;
    }
    
    private function getDownloadUrl(array $release_data): string
    {
        $url = $release_data['download_url'];
        
        // Add access token for private repositories
        if (Updater::GITHUB['token']) {
            $url = add_query_arg('access_token', Updater::GITHUB['token'], $url);
        }
        
        return esc_url_raw($url);
    }

    private function currentVersion(): string {
        $plugin_data = get_file_data(PLUGIN_FRAME_FILE, ['Version' => 'Version']);
        return $plugin_data['Version'] ?? '0.0.0';
    }
}