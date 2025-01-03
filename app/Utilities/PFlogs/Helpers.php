<?php

namespace PluginFrame\Utilities\PFlogs;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

require_once PLUGIN_FRAME_DIR . 'app/Utilities/PFlogs/PFlogs.php';
use PluginFrame\Utilities\PFlogs\PFlogs;

require_once PLUGIN_FRAME_DIR . 'app/Utilities/PFlogs/LogCleaner.php';
use PluginFrame\Utilities\PFlogs\LogCleaner;

require_once PLUGIN_FRAME_DIR . 'app/Services/Scheduler.php';
use PluginFrame\Services\Scheduler;

// Define `pf_log` function globally
if (!function_exists('pf_log')) {
    function pf_log($data): void
    {
        PFlogs::pf_log($data);
    }
}

// Define `pf_logs` function globally
if (!function_exists('pf_logs')) {
    function pf_logs($data): void
    {
        PFlogs::pf_log($data);
    }
}

// Initialize LogCleaner after all files are loaded
if (PFlogs::isLoggingEnabled()) {
    $scheduler = new Scheduler();
    new LogCleaner($scheduler); // Automatically schedules the log cleaner
} else {
    error_log('Unable to execute PF LogCleaner scheduler.');
}
