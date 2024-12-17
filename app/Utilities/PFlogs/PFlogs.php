<?php

namespace PluginFrame\Utilities;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

class PFlogs
{
    // Configuration
    private static bool $loggingEnabled = true; // Set to false to disable logging
    private static int $retentionDays = 7;      // Retain logs for this many days
    private static string $logsDir = PLUGIN_FRAME_DIR . '/logs'; // Directory for logs

    /**
     * Logs a message to the plugin frame logs.
     *
     * @param string $message The log message.
     * @return void
     */
    public static function pf_log(string $message): void
    {
        // Check if logging is enabled
        if (!self::$loggingEnabled) {
            return;
        }

        // Ensure logs directory exists
        if (!is_dir(self::$logsDir)) {
            mkdir(self::$logsDir, 0755, true);
        }

        // Get the current date for the log file name with month as text
        $date = date('Y-F-d'); // Format: YYYY-MonthName-DD (e.g., 2024-December-11)
        $logFile = self::$logsDir . "/{$date}.log";

        // Format the log message
        $time = date('Y-m-d H:i:s');
        $formattedMessage = "[{$time}] {$message}" . PHP_EOL;

        // Append the message to the log file
        file_put_contents($logFile, $formattedMessage, FILE_APPEND);
    }

    /**
     * Deletes log files older than the retention period.
     *
     * @return void
     */
    public static function delete_old_logs(): void
    {
        // Ensure logs directory exists
        if (!is_dir(self::$logsDir)) {
            return;
        }

        // Calculate the cutoff date
        $cutoffDate = strtotime('-' . self::$retentionDays . ' days');

        // Iterate over log files and delete old ones
        foreach (glob(self::$logsDir . '/*.log') as $logFile) {
            $fileName = basename($logFile, '.log');
            $fileDate = strtotime(str_replace('-', ' ', $fileName)); // Convert text date to timestamp

            if ($fileDate !== false && $fileDate < $cutoffDate) {
                unlink($logFile); // Delete the old log file
            }
        }
    }
}
