<?php

namespace Pluginframe\DB\Migrations;

use Exception;
use Pluginframe\DB\Models\BaseModel;
use Pluginframe\DB\Utils\QueryBuilder;
use Pluginframe\DB\Utils\DBHelper;

// Exit if accessed directly
if (!defined('ABSPATH')) { exit; }

/**
 * Migration class for creating and managing database migrations.
 */
class Migration
{
    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * Constructor to initialize shared dependencies.
     */
    public function __construct()
    {
        $this->queryBuilder = new QueryBuilder();
    }

    /**
     * Run the migration.
     */
    public function run()
    {
        try {
            $this->createJobCategoriesTable();
            $this->createJobListingsTable();
        } catch (Exception $e) {
            error_log("Migration failed: " . $e->getMessage());
        }
    }

    /**
     * Create the job categories table using QueryBuilder.
     */
    public function createJobCategoriesTable()
    {
        // Define the schema for the `job_categories` table
        $columns = [
            'ID BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'name VARCHAR(255) NOT NULL',
            'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            'updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
        ];

        $this->createTable('job_categories', $columns);
    }

    /**
     * Create the job listings table using QueryBuilder.
     */
    public function createJobListingsTable()
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
            'FOREIGN KEY (category_id) REFERENCES {prefix}job_categories(ID) ON DELETE CASCADE',
        ];

        $this->createTable('job_listings', $columns);
    }

    /**
     * Helper function to create tables dynamically using QueryBuilder.
     *
     * @param string $tableName
     * @param array $columns
     */
    private function createTable($tableName, $columns)
    {
        global $wpdb;

        $table = $wpdb->prefix . $tableName;
        $charsetCollate = $wpdb->get_charset_collate();

        // Use QueryBuilder to dynamically create the table
        $this->queryBuilder->table($table)
            ->createTable($columns, $charsetCollate);

        // Optional: Log success or add more complex logic for feedback
        error_log("Table '$tableName' created successfully.");
    }
}
