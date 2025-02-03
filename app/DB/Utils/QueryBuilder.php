<?php

namespace Pluginframe\DB\Utils;

/**
 * QueryBuilder class for building, executing dynamic SQL queries, and handling schema operations.
 */
class QueryBuilder
{
    private $table;
    private $columns = [];
    private $where = [];
    private $whereValues = [];
    private $limit;
    private $offset;
    private $orderBy = [];
    private $groupBy = [];
    private $joins = [];
    private $insertData = [];
    private $updateData = [];

    /**
     * Set the table name.
     *
     * @param string $table The table name.
     * @return $this
     */
    public function table($table)
    {
        $this->table = $table;
        return $this;
    }

    /**
     * Set the columns to select.
     *
     * @param array $columns The items is selected (default is all `*`).
     * @return $this
     */
    public function select($columns = ['*'])
    {
        $this->columns = $columns;
        return $this;
    }

    /**
     * Add a raw SELECT clause.
     *
     * @param string $raw SQL for a raw select statement.
     * @return $this
     */
    public function raw($raw)
    {
        $this->columns[] = $raw;
        return $this;
    }

    /**
     * Add a raw SELECT clause.
     *
     * @param string $raw SQL for a raw select statement.
     * @return $this
     */
    public function selectRaw($raw)
    {
        return $this->raw($raw);
    }

    /**
     * Add a WHERE clause.
     *
     * @param string $column The column name.
     * @param string $operatorOrValue The operator or the value (if two arguments are passed).
     * @param mixed|null $value The value to compare against (if three arguments are passed).
     * @return $this
     */
    public function where($column, $operatorOrValue, $value = null)
    {
        // Check if the second argument is the operator or the value
        if (is_null($value)) {
            $operator = '=';
            $value = $operatorOrValue;
        } else {
            $operator = $operatorOrValue;
        }

        $this->where[] = "{$column} {$operator} %s";
        $this->whereValues[] = $value;
        return $this;
    }
    
    /**
     * Add a GROUP BY clause.
     *
     * @param string|array $columns The column(s) to group by.
     * @return $this
     */
    public function groupBy($columns)
    {
        if (is_array($columns)) {
            $this->groupBy = array_merge($this->groupBy, $columns);
        } else {
            $this->groupBy[] = $columns;
        }
        return $this;
    }

    /**
     * Add a Column BY clause.
     *
     * @param string|array $columns The column(s) to group by.
     * @return $this
     */
    public function columns($columns)
    {
        return $this->groupBy($columns);
    }

    /**
     * Add an ORDER BY clause.
     *
     * @param string $column The column name.
     * @param string $direction The sort direction (ASC or DESC).
     * @return $this
     */
    public function orderBy($column, $direction = 'ASC')
    {
        $this->orderBy[] = "{$column} {$direction}";
        return $this;
    }

    /**
     * Add a INNER JOIN clause.
     *
     * @param string $table The table to join.
     * @param string $first The first column for the join.
     * @param string $operator The comparison operator.
     * @param string $second The second column for the join.
     * @param string $type The join type for the join.
     * @return $this
     */
    public function join($table, $first, $operator, $second, $type = 'INNER')
    {
        $this->joins[] = "{$type} JOIN {$table} ON {$first} {$operator} {$second}";
        return $this;
    }

    /**
     * Add a LEFT JOIN clause.
     *
     * @param string $table The table to join.
     * @param string $first The first column for the join.
     * @param string $operator The comparison operator.
     * @param string $second The second column for the join.
     * @return $this
     */
    public function leftJoin($table, $first, $operator, $second)
    {
        return $this->join($table, $first, $operator, $second, 'LEFT');
    }

    /**
     * Add a RIGHT JOIN clause.
     *
     * @param string $table The table to join.
     * @param string $first The first column for the join.
     * @param string $operator The comparison operator.
     * @param string $second The second column for the join.
     * @return $this
     */
    public function rightJoin($table, $first, $operator, $second)
    {
        return $this->join($table, $first, $operator, $second, 'RIGHT');
    }

    /**
     * Set the limit for the query.
     *
     * @param int $limit The limit.
     * @return $this
     */
    public function limit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Set the offset for the query (for pagination).
     *
     * @param int $offset The offset.
     * @return $this
     */
    public function offset($offset)
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * Insert data into the table.
     *
     * @param array $data Associative array of column => value.
     * @return int|false The inserted row ID or false on failure.
     */
    public function insert(array $data)
    {
        global $wpdb;

        $this->insertData = $data;
        $table = $wpdb->prefix . $this->table;

        $result = $wpdb->insert($table, $data);

        return $result ? $wpdb->insert_id : false;
    }

    /**
     * Update data in the table.
     *
     * @param array $data Associative array of column => value.
     * @return int|false Number of rows updated or false on failure.
     */
    public function update(array $data)
    {
        global $wpdb;

        $this->updateData = $data;
        $table = $wpdb->prefix . $this->table;

        $where = implode(' AND ', $this->where);
        $result = $wpdb->update($table, $data, $this->getWhereBindings());

        return $result !== false ? $result : false;
    }

    /**
     * Delete rows from the table.
     *
     * @return int|false Number of rows deleted or false on failure.
     */
    public function delete()
    {
        global $wpdb;

        $table = $wpdb->prefix . $this->table;
        $where = implode(' AND ', $this->where);

        return $wpdb->query($wpdb->prepare("DELETE FROM $table WHERE $where", ...$this->whereValues));
    }

    /**
     * Execute the query and return results.
     *
     * @return array
     */
    public function get()
    {
        global $wpdb;

        // Ensure columns are set properly, default to '*'
        $columns = !empty($this->columns) ? implode(', ', $this->columns) : '*';

        $sql = "SELECT {$columns} FROM {$wpdb->prefix}{$this->table}";
        
        if (!empty($this->joins)) {
            $sql .= " " . implode(' ', $this->joins);
        }

        if (!empty($this->where)) {
            $sql .= " WHERE " . implode(' AND ', $this->where);
        }

        if (!empty($this->groupBy)) {
            $sql .= " GROUP BY " . implode(', ', $this->groupBy);
        }

        if (!empty($this->orderBy)) {
            $sql .= " ORDER BY " . implode(', ', $this->orderBy);
        }

        if ($this->limit) {
            $sql .= " LIMIT {$this->limit}";
        }

        if ($this->offset) {
            $sql .= " OFFSET {$this->offset}";
        }

        return $wpdb->get_results($wpdb->prepare($sql, ...$this->whereValues), ARRAY_A);
    }

    
    /**
     * Execute the query and return results.
     *
     * @return array
     */
    public function execute()
    {
        return $this->get();
    }


    /**
     * Get the total number of records for the query (without LIMIT/OFFSET).
     *
     * @return int The total number of records.
     */
    public function count()
    {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}{$this->table}";

        if (!empty($this->joins)) {
            $sql .= " " . implode(' ', $this->joins);
        }

        if (!empty($this->where)) {
            $sql .= " WHERE " . implode(' AND ', $this->where);
        }

        return $wpdb->get_var($wpdb->prepare($sql, ...$this->whereValues));
    }

    /**
     * Create a table with the specified columns and charset/collation.
     *
     * @param array $columns The column definitions.
     * @param string $charsetCollate The charset and collation for the table.
     * @return void
     */
    public function createTable(array $columns, $charsetCollate)
    {
        global $wpdb;

        $columnsSql = implode(",\n", $columns);
        $sql = "CREATE TABLE {$wpdb->prefix}{$this->table} (\n{$columnsSql}\n) {$charsetCollate};";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Drop the table.
     *
     * @return void
     */
    public function dropTable()
    {
        global $wpdb;

        $sql = "DROP TABLE IF EXISTS {$wpdb->prefix}{$this->table};";
        $wpdb->query($sql);
    }

    /**
     * Truncate the table (delete all rows without dropping the structure).
     *
     * @return void
     */
    public function truncateTable()
    {
        global $wpdb;

        $sql = "TRUNCATE TABLE {$wpdb->prefix}{$this->table};";
        $wpdb->query($sql);
    }

    /**
     * Get WHERE bindings as an associative array for update/delete.
     *
     * @return array
     */
    private function getWhereBindings()
    {
        $bindings = [];
        foreach ($this->where as $index => $condition) {
            preg_match('/^(.+?)\s/', $condition, $matches);
            if (isset($matches[1])) {
                $bindings[$matches[1]] = $this->whereValues[$index];
            }
        }
        return $bindings;
    }
}
