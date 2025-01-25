<?php

namespace Pluginframe\DB\Migrations;

use Pluginframe\DB\Utils\QueryBuilder;

// Exit if accessed directly
if (!defined('ABSPATH')) { exit; }

/**
 * CreateJobCategoriesTable migration
 */
class CreateJobCategoriesTable
{
    protected $queryBuilder;

    public function __construct()
    {
        $this->queryBuilder = new QueryBuilder();
    }

    /**
     * Run the migration to create the job categories table.
     */
    public function run()
    {
        // Define the schema for the `job_categories` table
        $columns = [
            'ID BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'name VARCHAR(255) NOT NULL',
            'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            'updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
        ];

        // Use QueryBuilder to create the table
        $this->createTable('job_categories', $columns);
    }

    /**
     * Create a table using the provided table name and columns.
     *
     * @param string $tableName
     * @param array $columns
     */
    private function createTable($tableName, $columns)
    {
        global $wpdb;

        // Dynamically get the full table name with the WordPress prefix
        $table = $wpdb->prefix . $tableName;
        $charsetCollate = $wpdb->get_charset_collate();

        // Use QueryBuilder to generate the CREATE TABLE query
        $this->queryBuilder->table($table)
            ->createTable($columns, $charsetCollate);

        // Optionally log or handle success
        error_log("Table '$tableName' created successfully.");
    }
}
