<?php

namespace PluginFrame\Utilities\PFlogs;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

require_once PLUGIN_FRAME_DIR . 'app/Utilities/PFlogs/PFlogs.php';
use PluginFrame\Utilities\PFlogs;

require_once PLUGIN_FRAME_DIR . 'app/Services/Scheduler.php';
use PluginFrame\Services\Scheduler;

class LogCleaner
{
    // Cron hook name
    private static string $hookName = 'plugin_frame_log_cleaner';
    
    // Instance of Scheduler
    private Scheduler $scheduler;

    /**
     * Constructor
     *
     * @param Scheduler $scheduler
     */
    public function __construct(Scheduler $scheduler)
    {
        $this->scheduler = $scheduler;

        // Schedule the log cleaner
        add_action('init', [$this, 'scheduleLogCleaner']);
    }

    /**
     * Schedules the log cleaner cron job.
     *
     * @return void
     */
    public function scheduleLogCleaner(): void
    {
        // Check if already scheduled
        if (!$this->isScheduled()) {
            $this->scheduler->scheduleRecurring(self::$hookName, HOUR_IN_SECONDS, [$this, 'deleteLogs']);
        }

        // Register the task handler
        add_action(self::$hookName, [$this, 'deleteLogs']);
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
        return wp_next_scheduled(self::$hookName) !== false;
    }
}
