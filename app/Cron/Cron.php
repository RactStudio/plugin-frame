<?php

namespace PluginFrame\Cron;

use PluginFrame\Cron\Helpers;

class Cron
{
    private static string $cronFile = PLUGIN_FRAME_DIR . 'app/Cron/cron_jobs.json';
    private static string $logDir = PLUGIN_FRAME_DIR . 'logs/cron-logs/';
    private static string $taskerFile = PLUGIN_FRAME_DIR . 'app/Config/Cron.txt';
    private static bool $isEnabled = true; // Toggle cron system
    private static int $defaultRetention = 7; // Default log retention in days

    /**
     * Initialize the cron system.
     */
    public static function init(): void
    {
        // Ensure cron file, log directory, and tasker file exist
        if (!file_exists(self::$cronFile)) {
            Helpers::writeJson(self::$cronFile, []);
        }
        if (!file_exists(self::$logDir)) {
            mkdir(self::$logDir, 0755, true);
        }
        if (!file_exists(self::$taskerFile)) {
            file_put_contents(self::$taskerFile, self::getDefaultTaskerContent());
        }

        // Load tasks from tasker.txt
        self::loadTasksFromTasker();
    }

    /**
     * Load tasks from the tasker.txt file.
     */
    private static function loadTasksFromTasker(): void
    {
        $tasks = Helpers::readJson(self::$cronFile);
        $lines = file(self::$taskerFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            list($name, $interval, $callback) = explode('|', $line);
            if (!isset($tasks[$name])) {
                self::add($name, $callback, (int)$interval);
            }
        }
    }

    /**
     * Add a new cron job.
     */
    public static function add(string $name, string $callback, int $interval): void
    {
        $tasks = Helpers::readJson(self::$cronFile);
        $tasks[$name] = [
            'callback' => $callback,
            'interval' => $interval,
            'last_run' => 0,
        ];
        Helpers::writeJson(self::$cronFile, $tasks);
    }

    /**
     * Execute cron tasks based on their schedule.
     */
    public static function run(): void
    {
        $tasks = Helpers::readJson(self::$cronFile);

        foreach ($tasks as $name => $task) {
            $now = time();

            // Check if the task is due
            if ($now - $task['last_run'] >= $task['interval']) {
                try {
                    // Execute the task's callback
                    if (is_callable($task['callback'])) {
                        call_user_func($task['callback']);
                    }

                    // Update the last_run time
                    $tasks[$name]['last_run'] = $now;
                    self::log("Task '{$name}' executed successfully.");
                } catch (\Throwable $e) {
                    self::log("Error executing task '{$name}': " . $e->getMessage());
                }
            }
        }

        // Save updated tasks back to the file
        Helpers::writeJson(self::$cronFile, $tasks);
    }

    /**
     * Get default app/Config/Cron.txt content.
     */
    private static function getDefaultTaskerContent(): string
    {
        return <<<EOT
clean_pf_logs|86400|Cron::cleanPfLogs
clean_cron_logs|86400|Cron::cleanOldLogs
EOT;
    }

    /**
     * Example: Clean PF logs (add your actual implementation here).
     */
    public static function cleanPfLogs(): void
    {
        // Add your PF logs cleanup logic here.
    }

    /**
     * Clean old cron logs based on retention policy.
     */
    public static function cleanOldLogs(): void
    {
        $files = glob(self::$logDir . '*.log');
        foreach ($files as $file) {
            if (time() - filemtime($file) > self::$defaultRetention * 86400) {
                unlink($file);
                self::log("Deleted old log file: " . basename($file));
            }
        }
    }

    /**
     * Log messages to the cron log file.
     */
    private static function log(string $message): void
    {
        $date = date('Y-F-d');
        $filePath = self::$logDir . "{$date}.log";

        // Append log message
        $logEntry = "[" . date('Y-m-d H:i:s') . "] " . $message . PHP_EOL;
        file_put_contents($filePath, $logEntry, FILE_APPEND);
    }
}
