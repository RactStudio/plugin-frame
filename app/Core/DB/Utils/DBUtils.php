<?php

namespace PluginFrame\Core\DB\Utils;

/**
 * DBUtils class for utility methods related to the database.
 */
class DBUtils
{
    /**
     * Sanitize SQL query before execution.
     *
     * @param string $query The SQL query to sanitize.
     * @return string The sanitized query.
     */
    public function sanitizeQuery($query)
    {
        global $wpdb;
        return $wpdb->prepare($query);
    }

    /**
     * Check if a table exists in the database.
     *
     * @param string $table The table name.
     * @return bool Whether the table exists.
     */
    public function tableExists($table)
    {
        global $wpdb;
        $tableName = $wpdb->prefix . $table;
        $results = $wpdb->get_results("SHOW TABLES LIKE '{$tableName}'");
        return !empty($results);
    }
}
