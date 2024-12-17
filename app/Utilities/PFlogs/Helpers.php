<?php

namespace PluginFrame\Utilities;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

use PluginFrame\Utilities\PFlogs;

// Check and define `pf_log` only if it doesn't already exist
if (!function_exists('pf_log')) {
    /**
     * Error logs execute as native function
     */
    function pf_log($data): void
    {
        PFlogs::pf_log($data);
    }
}

// Check and define `pf_logs` only if it doesn't already exist
if (!function_exists('pf_logs')) {
    /**
     * Error logs execute as native function
     */
    function pf_logs($data): void
    {
        PFlogs::pf_log($data);
    }
}