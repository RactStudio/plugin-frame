<?php

namespace PluginFrame\DB\Models;

use Exception;
use PluginFrame\DB\Utils\QueryBuilder;

// Exit if accessed directly
if (!defined('ABSPATH')) { exit; }

/**
 * Base Model class to handle common database operations
 */
class BaseModel
{
    protected $table;
    protected $primaryKey = 'ID';
    protected $queryBuilder;

    public function __construct()
    {
        // Initialize the table name dynamically, in case of subclasses
        if (empty($this->table)) {
            throw new Exception('Table name must be specified.');
        }

        // Initialize QueryBuilder
        $this->queryBuilder = new QueryBuilder();
        $this->queryBuilder->table($this->table);
    }

    /**
     * Get all records from the model's table.
     *
     * @return array
     */
    public function all()
    {
        return $this->queryBuilder->select()->get();
    }

    /**
     * Find a record by primary key.
     *
     * @param int $id
     * @return mixed
     */
    public function find($id)
    {
        $result = $this->queryBuilder->where($this->primaryKey, '=', $id)->get();
        return $result ? $result[0] : null;
    }

    /**
     * Insert a record into the model's table.
     *
     * @param array $data
     * @return int|false Inserted ID or false on failure.
     */
    public function create(array $data)
    {
        return $this->queryBuilder->insert($data);
    }

    /**
     * Update a record by primary key.
     *
     * @param int $id
     * @param array $data
     * @return int|false Number of rows updated or false on failure.
     */
    public function update($id, array $data)
    {
        return $this->queryBuilder->where($this->primaryKey, '=', $id)->update($data);
    }

    /**
     * Delete a record by primary key.
     *
     * @param int $id
     * @return int|false Number of rows deleted or false on failure.
     */
    public function delete($id)
    {
        return $this->queryBuilder->where($this->primaryKey, '=', $id)->delete();
    }

    /**
     * Paginate records from the model's table.
     *
     * @param int $perPage Number of records per page.
     * @param int $currentPage Current page number.
     * @return array An array containing paginated records and metadata.
     */
    public function paginate($perPage = 10, $currentPage = 1)
    {
        $offset = ($currentPage - 1) * $perPage;
        $totalRecords = $this->queryBuilder->count();
        $totalPages = ceil($totalRecords / $perPage);

        $records = $this->queryBuilder
            ->limit($perPage)
            ->offset($offset)
            ->get();

        return [
            'data' => $records,
            'pagination' => [
                'total_records' => $totalRecords,
                'per_page' => $perPage,
                'current_page' => $currentPage,
                'total_pages' => $totalPages,
                'has_next_page' => $currentPage < $totalPages,
                'has_previous_page' => $currentPage > 1,
            ],
        ];
    }

    /**
     * Perform a custom JOIN query.
     *
     * @param string $table The table to join.
     * @param string $first The first column for the join condition.
     * @param string $operator The comparison operator.
     * @param string $second The second column for the join condition.
     * @param string $type The type of join (INNER, LEFT, RIGHT).
     * @return $this
     */
    public function join($table, $first, $operator, $second, $type = 'INNER')
    {
        $this->queryBuilder->join($table, $first, $operator, $second, $type);
        return $this;
    }
    public function leftJoin($table, $first, $operator, $second)
    {
        $this->queryBuilder->leftJoin($table, $first, $operator, $second);
        return $this;
    }
    public function rightJoin($table, $first, $operator, $second)
    {
        $this->queryBuilder->rightJoin($table, $first, $operator, $second);
        return $this;
    }
    
    /**
     * Fetch a list of records with custom filters.
     *
     * @param array $filters Associative array of column => value.
     * @return array
     */
    public function filter(array $filters)
    {
        foreach ($filters as $column => $value) {
            $this->queryBuilder->where($column, '=', $value);
        }
        return $this->queryBuilder->get();
    }
}
