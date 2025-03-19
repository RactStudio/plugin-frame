<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Declare the logs functions in the global

require_once PLUGIN_FRAME_DIR . 'app/Core/Utilities/PFlogs/PFlogs.php';
require_once PLUGIN_FRAME_DIR . 'app/Core/Services/Scheduler.php';
require_once PLUGIN_FRAME_DIR . 'app/Core/Utilities/PFlogs/LogCleaner.php';

use PluginFrame\Core\Utilities\PFlogs\PFlogs;
use PluginFrame\Core\Services\Scheduler;
use PluginFrame\Core\Utilities\PFlogs\LogCleaner;

// Define global functions
if (!function_exists('pf_log')) {
    function pf_log($data): void {
        PFlogs::pf_log($data);
    }
}

if (!function_exists('pf_logs')) {
    function pf_logs($data): void {
        PFlogs::pf_log($data);
    }
}

// Automatically schedules the log cleaner
if (PFlogs::isLoggingEnabled()) {
    $scheduler = new Scheduler();
    new LogCleaner($scheduler);
} else {
    error_log('Unable to execute PF LogCleaner scheduler.');
    pf_logs('Unable to execute PF LogCleaner scheduler.');
}
