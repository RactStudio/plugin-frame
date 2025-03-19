<?php

namespace PluginFrame\Config;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Updater {
    // Update sources (only one external source can be true)
    const SOURCES = [
        'core'    => true,  // Always enabled
        'github'  => false,
        'custom'  => false,
        'wp_org'  => false, // Auto Enabled. But, Auto Disabled when others are active
    ];
    
    // GitHub configuration
    const GITHUB = [
        'owner' => 'your-org',
        'repo'  => 'plugin-repo',
        'token' => '' // Leave empty for public repos
    ];
    
    // Custom API configuration
    const CUSTOM = [
        'endpoint'  => 'https://api.example.com/updates',
        'key'       => 'your-api-key'
    ];
    
    // Verification settings
    const VERIFICATION = [
        'check_signature' => true,
        'public_key'      => '-----BEGIN PUBLIC KEY-----...'
    ];
}