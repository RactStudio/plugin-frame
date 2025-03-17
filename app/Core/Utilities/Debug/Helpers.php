<?php

namespace PluginFrame\Core\Utilities\Debug;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

require_once PLUGIN_FRAME_DIR . 'app/Core/Utilities/Debug/Debugger.php';
use PluginFrame\Core\Utilities\Debug\Debugger;

// Check and define `dd` function globally
if (!function_exists('dd')) {
    /**
     * Dump and die. Stops execution.
     */
    function dd($data): void
    {
        Debugger::dd($data);
    }
}

// Check and define `d` function globally
if (!function_exists('d')) {
    /**
     * Dump data and continue execution.
     */
    function d($data): void
    {
        Debugger::d($data);
    }
}

// Check and define `debug_log` function globally
if (!function_exists('debug_log')) {
    /**
     * Log data into PHP's error log.
     */
    function debug_log($data): void
    {
        Debugger::log($data);
    }
}

// Check and define `json` function globally
if (!function_exists('json')) {
    /**
     * Pretty-prints JSON for debugging purposes.
     */
    function json($data): void
    {
        Debugger::json($data);
    }
}

// Check and define `backtrace` function globally
if (!function_exists('backtrace')) {
    /**
     * Output the backtrace for debugging purposes.
     */
    function backtrace(): void
    {
        Debugger::backtrace();
    }
}
