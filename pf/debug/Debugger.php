<?php

namespace PluginFrame;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Debugger
{
    /**
     * Dump and Die (dd): Outputs all given data and halts execution.
     *
     * @param mixed ...$data Arbitrary number of arguments to dump.
     */
    public static function dd(...$data)
    {
        self::output($data, true); // Halt after output
    }

    /**
     * Dump (d): Outputs all given data without halting execution.
     *
     * @param mixed ...$data Arbitrary number of arguments to dump.
     */
    public static function d(...$data)
    {
        self::output($data, false); // Continue execution
    }

    /**
     * Log data to the WordPress debug.log file.
     *
     * @param mixed ...$data Arbitrary number of arguments to log.
     */
    public static function log(...$data)
    {
        foreach ($data as $item) {
            if (is_array($item) || is_object($item)) {
                error_log(print_r($item, true)); // Log readable array/object
            } else {
                error_log((string)$item); // Convert to string if not array/object
            }
        }
    }

    /**
     * Pretty print a JSON representation of the data and halt execution.
     *
     * @param mixed ...$data Arbitrary number of arguments to encode in JSON.
     */
    public static function json(...$data)
    {
        header('Content-Type: application/json');

        // Try encoding data to JSON.
        $encoded = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        if (json_last_error() !== JSON_ERROR_NONE) {
            // Handle JSON encoding errors gracefully.
            echo json_encode([
                'error' => 'Failed to encode JSON.',
                'message' => json_last_error_msg(),
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        } else {
            echo $encoded;
        }

        die; // Halt execution
    }

    /**
     * Outputs a backtrace for debugging purposes.
     *
     * @param bool $toLog If true, logs the backtrace to the debug.log file.
     */
    public static function backtrace($toLog = false)
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        if ($toLog) {
            // Log backtrace in a readable format.
            self::log($backtrace);
        } else {
            // Output backtrace visually.
            echo '<pre style="background: #222; color: #0ff; padding: 10px; border-radius: 5px; font-size: 14px; overflow: auto;">';
            print_r($backtrace);
            echo '</pre>';
        }
    }

    /**
     * Handles outputting data in a formatted way.
     *
     * @param array $data The data to output.
     * @param bool $halt Whether to halt execution after output.
     */
    private static function output(array $data, $halt)
    {
        foreach ($data as $item) {
            echo '<pre style="background: #222; color: #0f0; padding: 10px; border-radius: 5px; font-size: 14px; overflow: auto;">';
            // Handle different data types gracefully.
            if (is_array($item)) {
                print_r($item);
            } elseif (is_object($item)) {
                var_dump($item);
            } else {
                echo htmlspecialchars((string)$item, ENT_QUOTES, 'UTF-8');
            }
            echo '</pre>';
        }

        if ($halt) {
            die; // Halt execution
        }
    }
}
