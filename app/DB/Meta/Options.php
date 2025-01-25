<?php

namespace Pluginframe\DB\Meta;

use Pluginframe\DB\Utils\QueryBuilder;
use Pluginframe\DB\Pagination\PaginationManager;

// Exit if accessed directly
if (!defined('ABSPATH')) { exit; }

class Options
{
    protected $queryBuilder;
    protected $paginationManager;
    protected $table = 'options';

    public function __construct()
    {
        $this->queryBuilder = new QueryBuilder();
        $this->paginationManager = new PaginationManager();
    }

    /**
     * Get all options with pagination.
     *
     * @param int $page The current page.
     * @param int $perPage The number of items per page.
     * @return array
     */
    public function allOptions($page = 1, $perPage = 10)
    {
        if (method_exists($this->paginationManager, 'getPaginatedResults')) {
            return $this->paginationManager->getPaginatedResults(
                $this->queryBuilder->table($this->table),
                $page,
                $perPage
            );
        }

        return $this->queryBuilder->table($this->table)->get();
    }

    /**
     * Get a specific option by name.
     *
     * @param string $optionName The option name.
     * @return mixed
     */
    public function getOption($optionName)
    {
        return $this->queryBuilder->table($this->table)->where('option_name', $optionName)->get();
    }

    /**
     * Update an option value by its name.
     *
     * @param string $optionName The option name.
     * @param mixed $optionValue The new value.
     * @return bool
     */
    public function updateOption($optionName, $optionValue)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->where('option_name', $optionName)
            ->update(['option_value' => $optionValue]);
    }

    /**
     * Insert a new option.
     *
     * @param string $optionName The option name.
     * @param mixed $optionValue The option value.
     * @return bool|int
     */
    public function insertOption($optionName, $optionValue)
    {
        return $this->queryBuilder->table($this->table)->insert([
            'option_name' => $optionName,
            'option_value' => $optionValue,
        ]);
    }

    /**
     * Delete an option by name.
     *
     * @param string $optionName The option name.
     * @return bool
     */
    public function deleteOption($optionName)
    {
        return $this->queryBuilder->table($this->table)->where('option_name', $optionName)->delete();
    }
}
