<?php

namespace PluginFrame\Utilities;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

use DateTime;

class PFlogs
{
    // Configuration
    public static bool $loggingEnabled = true; // Set to false to disable logging
    private static int $retentionDays = 7;      // Retain logs for this many days
    public static string $logsDir = PLUGIN_FRAME_DIR . '/logs'; // Directory for logs

    /**
     * Logs a message to the plugin frame logs.
     *
     * @param string $message The log message.
     * @return void
     */
    public static function pf_log(string $message): void
    {
        if (!self::$loggingEnabled) {
            return;
        }

        if (!is_dir(self::$logsDir)) {
            mkdir(self::$logsDir, 0755, true);
        }

        $date = date('d-F-Y'); 
        $logFile = self::$logsDir . "/{$date}.log";

        $time = date('Y-m-d H:i:s');
        $formattedMessage = "[{$time}] [Year-Month-Date] {$message}" . PHP_EOL;

        file_put_contents($logFile, $formattedMessage, FILE_APPEND);
    }

    /**
     * Deletes log files older than the retention period.
     *
     * @return void
     */
    public static function delete_old_logs(): void
    {
        pf_log('Start delete_old_logs');

        if (!is_dir(self::$logsDir)) {
            return;
        }
        
        $cutoffDate = strtotime('-' . self::$retentionDays . ' days');

        foreach (glob(self::$logsDir . '/*.log') as $logFile) {
            $fileName = basename($logFile, '.log');

            // Parse the file date from the log file name
            $fileDate = DateTime::createFromFormat('d-F-Y', $fileName);

            if ($fileDate && $fileDate->getTimestamp() < $cutoffDate) {
                unlink($logFile); // Delete old log file
            }
        }

        pf_log('End delete_old_logs');
    }

    /**
     * Returns whether logging is enabled.
     *
     * @return bool
     */
    public static function isLoggingEnabled(): bool
    {
        return self::$loggingEnabled;
    }
}
