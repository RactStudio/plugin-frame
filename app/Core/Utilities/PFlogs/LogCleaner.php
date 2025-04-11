<?php

namespace PluginFrame\Core\Utilities\PFlogs;

// Exit if accessed directly
if (!defined('ABSPATH')) { exit; }

require_once PLUGIN_FRAME_DIR . 'app/Core/Utilities/PFlogs/PFlogs.php';
use PluginFrame\Core\Utilities\PFlogs\PFlogs;

require_once PLUGIN_FRAME_DIR . 'app/Core/Services/Scheduler.php';
use PluginFrame\Core\Services\Scheduler;

class LogCleaner
{
    private static string $hookName = 'plugin_frame_log_cleaner';
    private Scheduler $scheduler;

    public function __construct(Scheduler $scheduler)
    {
        $this->scheduler = $scheduler;
        $this->registerHooks();
    }

    private function registerHooks(): void
    {
        add_action('init', [$this, 'scheduleLogCleaner']);
        add_action(self::$hookName, [$this, 'deleteLogs']);
    }

    /**
     * Schedules the log cleaner cron job.
     *
     * @return void
     */
    public function scheduleLogCleaner(): void
    {
        if (!$this->isScheduled()) {
            $this->scheduler->scheduleRecurring(
                self::$hookName, 
                3600, // 3600 seconds (1 hour)
                [$this, 'deleteLogs'],
                [] // Explicit empty arguments
            );
        }
    }

    /**
     * Deletes old log files based on retention period.
     *
     * @return void
     */
    public function deleteLogs(): void
    {
        pf_log('LogCleaner started deleting old logs.');

        PFlogs::delete_old_logs();

        pf_log('LogCleaner completed deleting old logs.');
    }

    /**
     * Unschedules the log cleaner cron job.
     *
     * @return void
     */
    public function unschedule(): void
    {
        $this->scheduler->clearScheduled(self::$hookName);
    }

    /**
     * Check if the log cleaner is already scheduled.
     *
     * @return bool
     */
    public function isScheduled(): bool
    {
        $next = wp_next_scheduled(self::$hookName, []);
        return ($next !== false) && ($next !== null);
    }
}
