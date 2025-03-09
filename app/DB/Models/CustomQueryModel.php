<?php

namespace PluginFrame\DB\Models;

use PluginFrame\DB\Models\BaseModel;
use PluginFrame\DB\Utils\QueryBuilder;

// Exit if accessed directly
if (!defined('ABSPATH')) { exit; }

/**
 * CustomQueryModel for executing custom database queries.
 */
class CustomQueryModel extends BaseModel
{
    protected $queryBuilder;

    public function __construct($table)
    {
        $this->table = $table; // Set table dynamically
        parent::__construct();

        $this->queryBuilder = new QueryBuilder();
    }

    /**
     * Execute a custom query and return results.
     *
     * @param string $query
     * @param array $params
     * @return array
     */
    public function runCustomQuery($query, array $params = [])
    {
        global $wpdb;
        return $wpdb->get_results($wpdb->prepare($query, $params), ARRAY_A);
    }

    /**
     * Drop the table (use with caution).
     */
    public function dropTable()
    {
        $this->queryBuilder->dropTable();
    }
}
