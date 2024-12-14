<?php

namespace PluginFrame\Cron;

use PluginFrame\Cron\Cron;

class Heartbeat
{
    private static bool $isRunning = false;

    /**
     * Start the heartbeat process.
     */
    public static function start(): void
    {
        if (self::$isRunning) {
            return; // Prevent multiple concurrent executions
        }

        self::$isRunning = true;

        try {
            Cron::run(); // Execute the scheduled tasks
        } catch (\Throwable $e) {
            pf_log("Heartbeat error: " . $e->getMessage());
        } finally {
            self::$isRunning = false;
        }
    }
}
