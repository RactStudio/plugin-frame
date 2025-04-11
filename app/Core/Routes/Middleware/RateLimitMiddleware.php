<?php

namespace PluginFrame\Core\Routes\Middleware;

use WP_Error;

class RateLimitMiddleware
{
    public function handle($request)
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown'; // Get the client's IP address
        if ($ip === 'unknown') {
            return new WP_Error('invalid_request', 'Could not determine client IP.', ['status' => 400]);
        }

        $limit = 100; // Define the request limit
        $timeWindow = 60; // Define the time window in seconds
        $key = 'rate_limit_' . md5($ip); // Create a unique key for this IP address

        // Retrieve the current request data
        $rateData = get_transient($key);

        if ($rateData === false) {
            // If no data exists, initialize it
            $rateData = [
                'count' => 1, // Start with the first request
                'start_time' => time(), // Set the current time as the start
            ];
            set_transient($key, $rateData, $timeWindow);
        } else {
            // Increment the request count
            $rateData['count']++;

            // Check if the current time is still within the allowed window
            if (time() - $rateData['start_time'] <= $timeWindow) {
                if ($rateData['count'] > $limit) {
                    return new WP_Error(
                        'rate_limit_exceeded',
                        'Rate limit exceeded. Please try again later.',
                        ['status' => 429]
                    );
                }
            } else {
                // Reset the rate data if the time window has passed
                $rateData = [
                    'count' => 1,
                    'start_time' => time(),
                ];
            }

            // Update the transient
            set_transient($key, $rateData, $timeWindow);
        }

        return true; // Allow the request to proceed
    }
}