<?php

namespace PluginFrame\Config;

// Exit if accessed directly.
if (!defined('ABSPATH')) { exit; }

/**
 * Class DBconfig
 * Handles database configuration and table name management.
 */
class DBconfig
{
    /**
     * Get the table name with the WordPress database prefix.
     *
     * @param string $name Table name without prefix.
     * @return string Full table name with prefix.
     */
    public static function tableName(string $name): string
    {
        global $wpdb;

        return $wpdb->prefix . $name;
    }
}
