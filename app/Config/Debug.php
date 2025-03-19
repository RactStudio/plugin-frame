<?php

namespace PluginFrame\Config;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Debug {
    // DEBUG configuration
    const DEBUG = [
        'core'    => true,  // Always enabled
        'github'  => false,
        'custom'  => false,
        'wp_org'  => false, // Auto Enabled. But, Auto Disabled when others are active
    ];
    
    // LOG configuration
    const LOG = [
        'owner' => 'your-org',
        'repo'  => 'plugin-repo',
        'token' => '' // Leave empty for public repos
    ];
}