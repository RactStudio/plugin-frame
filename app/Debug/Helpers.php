<?php

use PluginFrame\Debug\Debugger;

// Check and define `dd` only if it doesn't already exist
if (!function_exists('dd')) {
    /**
     * Dump and die. Stops execution.
     */
    function dd($data): void
    {
        Debugger::dd($data);
    }
}

// Check and define `d` only if it doesn't already exist
if (!function_exists('d')) {
    /**
     * Dump data and continue execution.
     */
    function d($data): void
    {
        Debugger::d($data);
    }
}

// Check and define `debug_log` only if it doesn't already exist
if (!function_exists('debug_log')) {
    /**
     * Log data into PHP's error log.
     */
    function debug_log($data): void
    {
        Debugger::log($data);
    }
}

// Check and define `json` only if it doesn't already exist
if (!function_exists('json')) {
    /**
     * Pretty-prints JSON for debugging purposes.
     */
    function json($data): void
    {
        Debugger::json($data);
    }
}

// Check and define `backtrace` only if it doesn't already exist
if (!function_exists('backtrace')) {
    /**
     * Output the backtrace for debugging purposes.
     */
    function backtrace(): void
    {
        Debugger::backtrace();
    }
}
