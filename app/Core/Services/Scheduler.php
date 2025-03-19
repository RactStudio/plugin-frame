<?php

namespace PluginFrame\Core\Services;

// Exit if accessed directly
if (!defined('ABSPATH')) { exit; }

class Scheduler
{
    private $registered_intervals = [];

    public function __construct()
    {
        add_filter('cron_schedules', [$this, 'registerCustomIntervals']);
    }

    /**
     * Register all custom intervals with WordPress
     */
    public function registerCustomIntervals($schedules)
    {
        foreach ($this->registered_intervals as $interval => $name) {
            if (!isset($schedules[$name])) {
                $schedules[$name] = [
                    'interval' => $interval,
                    'display'  => sprintf(__('Every %d seconds'), $interval)
                ];
            }
        }
        return $schedules;
    }

    /**
     * Register a custom interval type
     */
    private function registerInterval(int $interval): string
    {
        $interval_name = 'pf_scheduler_interval_' . $interval;
        if (!isset($this->registered_intervals[$interval])) {
            $this->registered_intervals[$interval] = $interval_name;
        }
        return $interval_name;
    }

    /**
     * Schedule a recurring cron job
     */
    public function scheduleRecurring(string $hook, int $interval, callable $callback, array $args = []): void
    {
        // Register the interval first
        $interval_name = $this->registerInterval($interval);
        
        // Add the action hook
        add_action($hook, $callback);
        
        // Schedule the event if not already scheduled
        if (!wp_next_scheduled($hook, $args)) {
            wp_schedule_event(
                time(), 
                $interval_name, 
                $hook, 
                $args
            );
        }
    }

    /**
     * Schedule a single cron job.
     */
    public function scheduleSingle(string $hook, int $time, callable $callback, array $args = []): void
    {
        add_action($hook, $callback);

        if (!wp_next_scheduled($hook, $args)) {
            wp_schedule_single_event($time, $hook, $args);
        }
    }

    /**
     * Clear a scheduled cron job.
     */
    public function clearScheduled(string $hook, array $args = []): void
    {
        $timestamp = wp_next_scheduled($hook, $args);
        if ($timestamp) {
            wp_unschedule_event($timestamp, $hook, $args);
        }
    }

    /**
     * Categorize and list schedules with pagination support.
     */
    public function listSchedules(string $prefix, string $status = '', int $page = 1, int $perPage = 10): array
    {
        $schedules = _get_cron_array();
        $categorized = [
            'pending'   => [],
            'draft'     => [],
            'publish'   => [],
            'completed' => [],
            'trash'     => [],
        ];

        if (is_array($schedules)) {
            foreach ($schedules as $timestamp => $cronHooks) {
                foreach ($cronHooks as $hook => $data) {
                    if (strpos($hook, $prefix) === 0) {
                        foreach ($data as $argsKey => $args) {
                            $scheduleStatus = $this->getScheduleStatus($timestamp);
                            $categorized[$scheduleStatus][] = [
                                'hook' => $hook,
                                'args' => $args['args'] ?? [],
                                'timestamp' => $timestamp,
                            ];
                        }
                    }
                }
            }
        }

        // Filter by status if provided
        $schedules = $status ? ($categorized[$status] ?? []) : array_merge(...array_values($categorized));

        // Paginate results
        $total = count($schedules);
        $offset = ($page - 1) * $perPage;
        $paginated = array_slice($schedules, $offset, $perPage);

        return [
            'total'    => $total,
            'page'     => $page,
            'per_page' => $perPage,
            'pages'    => ceil($total / $perPage),
            'data'     => $paginated,
        ];
    }

    /**
     * Determine the status of a schedule based on the timestamp.
     */
    private function getScheduleStatus(int $timestamp): string
    {
        $currentTime = time();

        if ($timestamp > $currentTime) {
            return 'pending';
        }

        if ($timestamp === $currentTime) {
            return 'publish';
        }

        if ($timestamp < $currentTime) {
            return 'completed';
        }

        return 'trash';
    }

    /**
     * Move a schedule to the trash.
     */
    public function trashSchedule(string $hook, array $args = []): void
    {
        $timestamp = wp_next_scheduled($hook, $args);
        if ($timestamp) {
            wp_unschedule_event($timestamp, $hook, $args);
        }
    }

    /**
     * Clear all trashed schedules.
     */
    public function clearTrash(string $prefix): void
    {
        $schedules = $this->listSchedules($prefix, 'trash');

        foreach ($schedules['data'] as $schedule) {
            wp_unschedule_event($schedule['timestamp'], $schedule['hook'], $schedule['args']);
        }
    }
}
