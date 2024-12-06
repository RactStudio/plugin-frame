<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Load Debugger File and Class
require_once PLUGIN_FRAME_DIR . 'pf/debug/Debugger.php';
use PluginFrame\Debugger;

if (!function_exists('dd')) {
    /**
     * Dump and Die (dd): Outputs the given data and halts execution.
     *
     * @param mixed ...$data One or more variables to dump.
     */
    function dd(...$data): void
    {
        Debugger::dd(...$data);
    }
}

if (!function_exists('d')) {
    /**
     * Dump (d): Outputs the given data without halting execution.
     *
     * @param mixed ...$data One or more variables to dump.
     */
    function d(...$data): void
    {
        Debugger::d(...$data);
    }
}

if (!function_exists('log')) {
    /**
     * Log data to the debug.log file.
     *
     * @param mixed ...$data One or more variables to log.
     */
    function log(...$data): void
    {
        Debugger::log(...$data);
    }
}

if (!function_exists('json')) {
    /**
     * Pretty print a JSON representation of data and halt execution.
     *
     * @param mixed ...$data One or more variables to encode as JSON.
     */
    function json(...$data): void
    {
        Debugger::json(...$data);
    }
}

if (!function_exists('backtrace'))
{
    /**
     * Backtrace: Outputs or logs a backtrace of the current execution stack.
     *
     * @param bool $toLog If true, logs the backtrace to the debug.log file.
     */
    function backtrace($toLog = false): void
    {
        Debugger::backtrace($toLog);
    }
}