<?php

namespace PluginFrame\DB\Migrations;

use PluginFrame\DB\Utils\QueryBuilder;

// Exit if accessed directly
if (!defined('ABSPATH')) { exit; }

/**
 * CreateJobListingsTable migration
 */
class CreateJobListingsTable
{
    protected $queryBuilder;

    public function __construct()
    {
        $this->queryBuilder = new QueryBuilder();
    }

    /**
     * Run the migration to create the job listings table.
     */
    public function run()
    {
        // Define the schema for the `job_listings` table
        $columns = [
            'ID BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'title VARCHAR(255) NOT NULL',
            'description TEXT NOT NULL',
            'category_id BIGINT(20) UNSIGNED',
            'location VARCHAR(255)',
            'salary DECIMAL(10,2)',
            'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            'updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
            'FOREIGN KEY (category_id) REFERENCES {prefix}job_categories(ID) ON DELETE CASCADE'
        ];

        // Use QueryBuilder to create the table
        $this->createTable('job_listings', $columns);
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
