<?php

namespace PluginFrame\Core\DB\Utils;

use PluginFrame\Core\DB\Utils\QueryBuilder;

/**
 * DBHelper class for providing helper methods for database tasks.
 */
class DBHelper
{
    protected $queryBuilder;

    public function __construct()
    {
        $this->queryBuilder = new QueryBuilder();
    }

    /**
     * Check if a column exists in a table.
     *
     * @param string $table The table name.
     * @param string $column The column name.
     * @return bool Whether the column exists.
     */
    public function columnExists($table, $column)
    {
        $results = $this->queryBuilder->table($table)
                                      ->select(['COLUMN_NAME'])
                                      ->where('COLUMN_NAME', '=', $column)
                                      ->get();

        return !empty($results);
    }

    /**
     * Add a column to a table if it doesn't exist.
     *
     * @param string $table The table name.
     * @param string $column The column name.
     * @param string $definition The column definition.
     * @return void
     */
    public function addColumnIfNotExists($table, $column, $definition)
    {
        if (!$this->columnExists($table, $column)) {
            $this->queryBuilder->table($table)
                               ->raw("ALTER TABLE {$table} ADD COLUMN {$column} {$definition}")
                               ->execute();
        }
    }
}
